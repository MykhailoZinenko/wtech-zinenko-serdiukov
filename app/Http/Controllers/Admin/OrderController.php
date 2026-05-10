<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $search = trim((string) $request->input('q'));
        $period = $request->input('period');
        $sort = $request->input('sort', 'newest');

        $query = Order::query()
            ->withCount('items')
            ->with('shippingMethod')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . $search . '%';
                $q->where(function ($inner) use ($like) {
                    $inner->where('order_number', 'ilike', $like)
                        ->orWhere('ship_first_name', 'ilike', $like)
                        ->orWhere('ship_last_name', 'ilike', $like)
                        ->orWhere('customer_email', 'ilike', $like);
                });
            })
            ->when($period, function ($q) use ($period) {
                $q->where('created_at', '>=', match ($period) {
                    'today' => now()->startOfDay(),
                    '7days' => now()->subDays(7),
                    '30days' => now()->subDays(30),
                    'month' => now()->startOfMonth(),
                    default => now()->subYears(10),
                });
            });

        $query = match ($sort) {
            'oldest' => $query->oldest(),
            'total_asc' => $query->orderBy('total'),
            'total_desc' => $query->orderByDesc('total'),
            default => $query->latest(),
        };

        $orders = $query->paginate(15)->withQueryString();

        $statusCounts = Order::query()
            ->selectRaw("status, count(*) as cnt")
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->all();
        $totalCount = array_sum($statusCounts);
        $pendingCount = $statusCounts['pending'] ?? 0;

        return view('admin.orders.index', [
            'orders' => $orders,
            'statusCounts' => $statusCounts,
            'totalCount' => $totalCount,
            'pendingCount' => $pendingCount,
            'filters' => compact('status', 'search', 'period', 'sort'),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items.product.primaryImage', 'shippingMethod', 'paymentMethod', 'statusHistory.changedBy', 'user']);

        $customerOrderCount = $order->user_id
            ? Order::where('user_id', $order->user_id)->count()
            : Order::where('customer_email', $order->customer_email)->count();

        return view('admin.orders.show', [
            'order' => $order,
            'customerOrderCount' => $customerOrderCount,
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,processing,shipped,delivered,cancelled,refunded'],
            'note' => ['nullable', 'string', 'max:1000'],
            'notify_customer' => ['nullable'],
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        if ($oldStatus !== $newStatus) {
            $order->update(['status' => $newStatus]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'changed_by_id' => auth()->id(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'note' => $validated['note'] ?? null,
                'customer_notified' => $request->boolean('notify_customer'),
                'created_at' => now(),
            ]);

            if ($newStatus === 'shipped') {
                $order->update(['shipped_at' => now()]);
            } elseif ($newStatus === 'delivered') {
                $order->update(['delivered_at' => now()]);
            } elseif ($newStatus === 'cancelled') {
                $order->update(['cancelled_at' => now()]);
            }
        }

        return back()->with('admin_success', 'Order status updated.');
    }
}
