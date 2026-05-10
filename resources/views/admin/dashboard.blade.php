@extends('layouts.admin')

@section('title', 'Dashboard')

@php
    $fmt = fn ($n) => number_format((int) $n, 0, ',', ' ');
    $maxRevenue = count($weeklyRevenue) ? max(1, max(array_values($weeklyRevenue))) : 1;
    $statusClass = [
        'pending' => 'status-pending',
        'processing' => 'status-processing',
        'shipped' => 'status-shipped',
        'delivered' => 'status-delivered',
        'cancelled' => 'status-cancelled',
        'refunded' => 'badge--grey',
    ];
@endphp

@section('content')
<div class="page-hdr">
    <div class="page-hdr__left">
        <h1 class="page-hdr__title">Dashboard</h1>
        <p class="page-hdr__sub">Welcome back — here's what's happening in the Emporium.</p>
    </div>
    <div class="page-hdr__actions">
        <a href="{{ route('admin.products.create') }}" class="btn-base btn-gold"><i class="bi bi-plus-lg"></i> Add Product</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card stat-card--gold">
        <div class="stat-card__icon"><i class="bi bi-coin"></i></div>
        <div class="stat-card__label">Revenue (30d)</div>
        <div class="stat-card__value">{{ $fmt($revenue) }}</div>
    </div>
    <div class="stat-card stat-card--green">
        <div class="stat-card__icon"><i class="bi bi-receipt"></i></div>
        <div class="stat-card__label">Orders (30d)</div>
        <div class="stat-card__value">{{ $fmt($orderCount) }}</div>
    </div>
    <div class="stat-card stat-card--blue">
        <div class="stat-card__icon"><i class="bi bi-box-seam"></i></div>
        <div class="stat-card__label">Products</div>
        <div class="stat-card__value">{{ $fmt($productCount) }}</div>
        @if ($lowStockCount > 0)
            <div class="stat-card__delta"><i class="bi bi-dot"></i> {{ $lowStockCount }} low stock</div>
        @endif
    </div>
    <div class="stat-card stat-card--red">
        <div class="stat-card__icon"><i class="bi bi-people"></i></div>
        <div class="stat-card__label">Customers</div>
        <div class="stat-card__value">{{ $fmt($customerCount) }}</div>
        @if ($newCustomersThisWeek > 0)
            <div class="stat-card__delta stat-card__delta--up"><i class="bi bi-arrow-up-short"></i> {{ $newCustomersThisWeek }} new this week</div>
        @endif
    </div>
</div>

<div class="dashboard-grid">
    <div class="flex-col">
        <div class="adm-card">
            <div class="adm-card__hdr">
                <span class="adm-card__title">Revenue — Last 7 Days</span>
                <span class="chart-unit">Crowns</span>
            </div>
            <div class="adm-card__body">
                <div class="chart-wrap">
                    @foreach ($weeklyRevenue as $day => $total)
                        <div class="chart-col">
                            <div class="chart-col__bar" style="height:{{ $maxRevenue > 0 ? round(($total / $maxRevenue) * 100) : 0 }}%;" data-val="{{ $fmt($total) }}"></div>
                            <span class="chart-col__lbl">{{ $day }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__hdr">
                <span class="adm-card__title">Recent Orders</span>
                <a href="#" class="btn-base btn-outline-gold btn-sm">View All</a>
            </div>
            @if ($recentOrders->isEmpty())
                <div class="adm-card__body">
                    <p style="color:var(--clr-text-dim);">No orders yet.</p>
                </div>
            @else
                <div class="tbl-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th class="col-hide-md">Items</th>
                                <th class="col-hide-md">Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentOrders as $order)
                                <tr>
                                    <td><span class="tbl-link td-heading">{{ $order->order_number }}</span></td>
                                    <td>{{ $order->ship_first_name }} {{ $order->ship_last_name }}</td>
                                    <td class="col-hide-md">{{ $order->items_count ?? $order->items()->count() }}</td>
                                    <td class="col-hide-md td-gold">{{ $fmt($order->total) }} ℂ</td>
                                    <td><span class="badge {{ $statusClass[$order->status] ?? 'badge--grey' }}">{{ ucfirst($order->status) }}</span></td>
                                    <td><span class="btn-base btn-outline-gold btn-sm btn-icon"><i class="bi bi-arrow-right"></i></span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="flex-col">
        <div class="adm-card">
            <div class="adm-card__hdr">
                <span class="adm-card__title">Activity Feed</span>
            </div>
            <div class="adm-card__body adm-card__body--tight">
                <div class="activity-list">
                    @forelse ($recentOrders->take(3) as $order)
                        <div class="activity-item">
                            <div class="activity-dot activity-dot--green"></div>
                            <div class="activity-text">
                                <strong>Order {{ $order->order_number }}</strong> placed by {{ $order->ship_first_name }} {{ $order->ship_last_name }}
                                <span class="activity-time">{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="activity-item">
                            <div class="activity-dot activity-dot--gold"></div>
                            <div class="activity-text">No recent activity.</div>
                        </div>
                    @endforelse
                    @foreach ($lowStockProducts->take(2) as $product)
                        <div class="activity-item">
                            <div class="activity-dot {{ $product->stock === 0 ? 'activity-dot--red' : 'activity-dot--gold' }}"></div>
                            <div class="activity-text">
                                <strong>{{ $product->name }}</strong> {{ $product->stock === 0 ? 'is out of stock' : "stock is low ({$product->stock})" }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__hdr">
                <span class="adm-card__title">Low Stock Alert</span>
                <a href="{{ route('admin.products.index') }}" class="btn-base btn-outline-gold btn-sm">Manage</a>
            </div>
            @if ($lowStockProducts->isEmpty())
                <div class="adm-card__body">
                    <p style="color:var(--clr-text-dim);">All products are well stocked.</p>
                </div>
            @else
                <div class="adm-card__body">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lowStockProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td><span class="{{ $product->stock === 0 ? 'stock-none' : 'stock-low' }} td-heading">{{ $product->stock }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
