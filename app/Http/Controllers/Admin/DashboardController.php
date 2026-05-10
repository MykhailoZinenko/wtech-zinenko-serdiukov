<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $thirtyDaysAgo = now()->subDays(30);
        $sixtyDaysAgo = now()->subDays(60);

        $validOrders = fn ($q) => $q->whereNotIn('status', ['cancelled', 'refunded']);

        $revenue = (int) $validOrders(Order::where('created_at', '>=', $thirtyDaysAgo))->sum('total');
        $prevRevenue = (int) $validOrders(Order::whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo]))->sum('total');

        $orderCount = $validOrders(Order::where('created_at', '>=', $thirtyDaysAgo))->count();
        $prevOrderCount = $validOrders(Order::whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo]))->count();

        $revenueDelta = $prevRevenue > 0 ? round(($revenue - $prevRevenue) / $prevRevenue * 100, 1) : null;
        $orderDelta = $prevOrderCount > 0 ? round(($orderCount - $prevOrderCount) / $prevOrderCount * 100, 1) : null;
        $productCount = Product::count();
        $customerCount = User::where('role', 'customer')->count();
        $lowStockCount = Product::whereColumn('stock', '<=', 'low_stock_threshold')->count();
        $newCustomersThisWeek = User::where('role', 'customer')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $recentOrders = Order::latest()->limit(5)->get();

        $lowStockProducts = Product::whereColumn('stock', '<=', 'low_stock_threshold')
            ->orderBy('stock')
            ->limit(5)
            ->get();

        $weeklyRevenue = $this->buildWeeklyRevenue();

        return view('admin.dashboard', [
            'revenue' => $revenue,
            'revenueDelta' => $revenueDelta,
            'orderCount' => $orderCount,
            'orderDelta' => $orderDelta,
            'productCount' => $productCount,
            'customerCount' => $customerCount,
            'lowStockCount' => $lowStockCount,
            'newCustomersThisWeek' => $newCustomersThisWeek,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'weeklyRevenue' => $weeklyRevenue,
        ]);
    }

    private function buildWeeklyRevenue(): array
    {
        $days = collect(CarbonPeriod::create(now()->subDays(6)->startOfDay(), now()))
            ->mapWithKeys(fn ($d) => [$d->format('D') => 0])
            ->all();

        $rows = Order::where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->selectRaw("to_char(created_at, 'Dy') as day_label, SUM(total) as total")
            ->groupByRaw("to_char(created_at, 'Dy')")
            ->pluck('total', 'day_label')
            ->all();

        foreach ($rows as $day => $total) {
            if (isset($days[$day])) {
                $days[$day] = (int) $total;
            }
        }

        return $days;
    }
}
