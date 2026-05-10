<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $thirtyDaysAgo = now()->subDays(30);

        $revenue = (int) Order::where('created_at', '>=', $thirtyDaysAgo)->sum('total');
        $orderCount = Order::where('created_at', '>=', $thirtyDaysAgo)->count();
        $productCount = Product::count();
        $customerCount = User::where('role', 'customer')->count();
        $lowStockCount = Product::where('stock', '<=', DB::raw('low_stock_threshold'))->count();

        $recentOrders = Order::with('shippingMethod')
            ->latest()
            ->limit(5)
            ->get();

        $lowStockProducts = Product::where('stock', '<=', DB::raw('low_stock_threshold'))
            ->orderBy('stock')
            ->limit(5)
            ->get();

        $weeklyRevenue = Order::where('created_at', '>=', now()->subDays(7))
            ->selectRaw("to_char(created_at, 'Dy') as day_label, SUM(total) as total")
            ->groupByRaw("to_char(created_at, 'Dy'), to_char(created_at, 'D')")
            ->orderByRaw("to_char(created_at, 'D')")
            ->pluck('total', 'day_label')
            ->all();

        return view('admin.dashboard', [
            'revenue' => $revenue,
            'orderCount' => $orderCount,
            'productCount' => $productCount,
            'customerCount' => $customerCount,
            'lowStockCount' => $lowStockCount,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'weeklyRevenue' => $weeklyRevenue,
        ]);
    }
}
