@extends('layouts.admin')

@section('title', 'Orders')

@php
    $fmt = fn ($n) => number_format((int) $n, 0, ',', ' ');
    $statusClass = [
        'pending' => 'status-pending',
        'processing' => 'status-processing',
        'shipped' => 'status-shipped',
        'delivered' => 'status-delivered',
        'cancelled' => 'status-cancelled',
        'refunded' => 'badge--grey',
    ];
    $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
@endphp

@section('content')
<div class="page-hdr">
    <div class="page-hdr__left">
        <h1 class="page-hdr__title">Orders</h1>
        <p class="page-hdr__sub">{{ $fmt($totalCount) }} orders · {{ $pendingCount }} pending action</p>
    </div>
</div>

<div class="adm-card">
    <div class="status-tabs">
        <a href="{{ route('admin.orders.index', array_merge(request()->except('status', 'page'))) }}" class="status-tab {{ !$filters['status'] ? 'active' : '' }}">All <span class="tab-count">{{ $totalCount }}</span></a>
        @foreach ($statuses as $s)
            <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => $s])) }}" class="status-tab {{ $filters['status'] === $s ? 'active' : '' }}">{{ ucfirst($s) }} <span class="tab-count">{{ $statusCounts[$s] ?? 0 }}</span></a>
        @endforeach
    </div>

    <form class="tbl-toolbar" method="GET">
        @if ($filters['status'])
            <input type="hidden" name="status" value="{{ $filters['status'] }}" />
        @endif
        <div class="tbl-search">
            <i class="bi bi-search"></i>
            <input type="search" name="q" value="{{ $filters['search'] }}" placeholder="Order #, customer name…" />
        </div>
        <select name="period" class="input-base select-arrow select-input" onchange="this.form.submit()">
            <option value="">All Time</option>
            <option value="today" @selected($filters['period'] === 'today')>Today</option>
            <option value="7days" @selected($filters['period'] === '7days')>Last 7 Days</option>
            <option value="30days" @selected($filters['period'] === '30days')>Last 30 Days</option>
            <option value="month" @selected($filters['period'] === 'month')>This Month</option>
        </select>
        <select name="sort" class="input-base select-arrow select-input ms-auto" onchange="this.form.submit()">
            <option value="newest" @selected($filters['sort'] === 'newest')>Sort: Newest</option>
            <option value="oldest" @selected($filters['sort'] === 'oldest')>Sort: Oldest</option>
            <option value="total_asc" @selected($filters['sort'] === 'total_asc')>Sort: Total ↑</option>
            <option value="total_desc" @selected($filters['sort'] === 'total_desc')>Sort: Total ↓</option>
        </select>
    </form>

    <div class="tbl-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th class="col-hide-md">Date</th>
                    <th class="col-hide-sm">Items</th>
                    <th class="col-hide-md">Shipping</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="tbl-link td-num">{{ $order->order_number }}</a></td>
                        <td>
                            <div>
                                <span>{{ $order->ship_first_name }} {{ $order->ship_last_name }}</span>
                                <span class="td-sub">{{ $order->customer_email }}</span>
                            </div>
                        </td>
                        <td class="col-hide-md td-dim">{{ $order->created_at->format('d M Y') }}<br><span class="td-sub">{{ $order->created_at->format('H:i') }}</span></td>
                        <td class="col-hide-sm td-dim">{{ $order->items_count }}</td>
                        <td class="col-hide-md td-dim">{{ $order->shippingMethod?->name }}</td>
                        <td class="td-gold">{{ $fmt($order->total) }} ℂ</td>
                        <td><span class="badge {{ $statusClass[$order->status] ?? 'badge--grey' }}">{{ ucfirst($order->status) }}</span></td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="btn-base btn-outline-gold btn-sm btn-icon" title="View order"><i class="bi bi-arrow-right"></i></a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="td-dim">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($orders->hasPages())
        <div class="pagination-bar">
            <span>Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $fmt($orders->total()) }} orders</span>
            <div class="pagination-btns">
                @if ($orders->onFirstPage())
                    <span class="pg-disabled"><i class="bi bi-chevron-left"></i></span>
                @else
                    <a href="{{ $orders->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a>
                @endif

                @foreach ($orders->getUrlRange(max(1, $orders->currentPage() - 2), min($orders->lastPage(), $orders->currentPage() + 2)) as $page => $url)
                    @if ($page === $orders->currentPage())
                        <span class="pg-active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($orders->currentPage() + 2 < $orders->lastPage())
                    <span class="pg-ellipsis">…</span>
                    <a href="{{ $orders->url($orders->lastPage()) }}">{{ $orders->lastPage() }}</a>
                @endif

                @if ($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a>
                @else
                    <span class="pg-disabled"><i class="bi bi-chevron-right"></i></span>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
