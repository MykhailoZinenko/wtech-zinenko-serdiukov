@extends('layouts.admin')

@section('title', 'Order ' . $order->order_number)

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
    $paymentStatusBadge = [
        'pending' => 'badge--orange',
        'paid' => 'badge--green',
        'failed' => 'badge--red',
        'refunded' => 'badge--grey',
    ];
@endphp

@section('content')
<div class="breadcrumb-adm">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <a href="{{ route('admin.orders.index') }}">Orders</a>
    <i class="bi bi-chevron-right"></i>
    <span class="bc-current">{{ $order->order_number }}</span>
</div>

<div class="page-hdr">
    <div class="page-hdr__left">
        <h1 class="page-hdr__title">Order {{ $order->order_number }} &nbsp;<span class="badge {{ $statusClass[$order->status] ?? 'badge--grey' }}">{{ ucfirst($order->status) }}</span></h1>
        <p class="page-hdr__sub">Placed {{ $order->created_at->format('d M Y') }} at {{ $order->created_at->format('H:i') }} &nbsp;·&nbsp; {{ $order->ship_first_name }} {{ $order->ship_last_name }}</p>
    </div>
</div>

@if (session('admin_success'))
    <div class="form-alert form-alert--success">{{ session('admin_success') }}</div>
@endif

<div class="order-grid">
    <div class="flex-col">
        <div class="adm-card">
            <div class="adm-card__hdr">
                <span class="adm-card__title">Order Items</span>
            </div>
            <div class="tbl-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="col-hide-sm">Qty</th>
                            <th class="col-hide-sm">Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <div class="tbl-product">
                                        <div class="tbl-thumb">
                                            @if ($item->product?->display_image_url)
                                                <img src="{{ $item->product->display_image_url }}" alt="{{ $item->product_name }}" />
                                            @else
                                                <div class="placeholder-asset">IMG</div>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="tbl-product__name">{{ $item->product_name }}</span>
                                            <span class="tbl-product__cat">{{ $item->product_sku }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-hide-sm">{{ $item->quantity }}</td>
                                <td class="td-dim col-hide-sm">{{ $fmt($item->unit_price) }} ℂ</td>
                                <td class="td-gold">{{ $fmt($item->line_total) }} ℂ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="order-totals order-totals--padded">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span class="total-row__amt">{{ $fmt($order->subtotal) }} ℂ</span>
                </div>
                <div class="total-row">
                    <span>Shipping — {{ $order->shippingMethod?->name }}</span>
                    <span class="total-row__amt">{{ $fmt($order->shipping_cost) }} ℂ</span>
                </div>
                @if ($order->discount > 0)
                    <div class="total-row">
                        <span>Discount</span>
                        <span class="total-row__amt td-green">— {{ $fmt($order->discount) }} ℂ</span>
                    </div>
                @endif
                <div class="total-row total-row--grand">
                    <span>Total</span>
                    <span class="total-row__amt">{{ $fmt($order->total) }} ℂ</span>
                </div>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__hdr">
                <span class="adm-card__title">Order Timeline</span>
            </div>
            <div class="adm-card__body">
                <div class="order-timeline">
                    @forelse ($order->statusHistory->sortBy('created_at') as $entry)
                        <div class="ot-item">
                            <div class="ot-dot ot-dot--done"><i class="bi bi-check-lg"></i></div>
                            <div class="ot-info">
                                <div class="ot-title">{{ ucfirst($entry->new_status) }}</div>
                                <div class="ot-time">
                                    {{ $entry->created_at->format('d M Y, H:i') }}
                                    @if ($entry->changedBy)
                                        — by {{ $entry->changedBy->full_name }}
                                    @endif
                                    @if ($entry->note)
                                        — {{ $entry->note }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="ot-item">
                            <div class="ot-dot ot-dot--done"><i class="bi bi-check-lg"></i></div>
                            <div class="ot-info">
                                <div class="ot-title">Order Placed</div>
                                <div class="ot-time">{{ $order->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="flex-col">
        <div class="adm-card">
            <div class="adm-card__hdr">
                <span class="adm-card__title">Update Status</span>
            </div>
            <div class="adm-card__body">
                <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-field">
                        <label for="order-status-select">New Status</label>
                        <select id="order-status-select" name="status" class="select-arrow">
                            @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'] as $s)
                                <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="status-note">Internal Note <span class="lbl-opt">(optional)</span></label>
                        <textarea id="status-note" name="note" rows="3" placeholder="e.g. Tracking number, carrier notes…"></textarea>
                    </div>
                    <label class="form-check-wwe">
                        <input type="checkbox" name="notify_customer" value="1" checked />
                        Notify customer by email
                    </label>
                    <button type="submit" class="btn-base btn-gold btn-full mt-3">
                        <i class="bi bi-check-lg"></i> Save Status
                    </button>
                </form>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__hdr">
                <span class="adm-card__title">Customer</span>
            </div>
            <div class="adm-card__body">
                <div class="info-rows">
                    <div class="info-row">
                        <span class="info-row__lbl">Name</span>
                        <span class="info-row__val">{{ $order->ship_first_name }} {{ $order->ship_last_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-row__lbl">Email</span>
                        <span class="info-row__val td-dim">{{ $order->customer_email }}</span>
                    </div>
                    @if ($order->customer_phone)
                        <div class="info-row">
                            <span class="info-row__lbl">Phone</span>
                            <span class="info-row__val">{{ $order->customer_phone }}</span>
                        </div>
                    @endif
                    <div class="info-row">
                        <span class="info-row__lbl">Orders</span>
                        <span class="info-row__val">{{ $customerOrderCount }} lifetime</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__hdr">
                <span class="adm-card__title">Shipping Address</span>
            </div>
            <div class="adm-card__body">
                <div class="info-address">
                    {{ $order->ship_first_name }} {{ $order->ship_last_name }}<br>
                    {{ $order->ship_street }}<br>
                    {{ $order->ship_city }}, {{ $order->ship_postal_code }}<br>
                    {{ ucfirst($order->ship_region) }}
                </div>
                <hr class="divider" />
                <div class="info-rows">
                    <div class="info-row">
                        <span class="info-row__lbl">Method</span>
                        <span class="info-row__val">{{ $order->shippingMethod?->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-row__lbl">Cost</span>
                        <span class="info-row__val">{{ $fmt($order->shipping_cost) }} ℂ</span>
                    </div>
                    <div class="info-row">
                        <span class="info-row__lbl">Tracking</span>
                        <span class="info-row__val td-dim">{{ $order->tracking_number ?: 'Not yet assigned' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__hdr">
                <span class="adm-card__title">Payment</span>
            </div>
            <div class="adm-card__body">
                <div class="info-rows">
                    <div class="info-row">
                        <span class="info-row__lbl">Method</span>
                        <span class="info-row__val">{{ $order->paymentMethod?->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-row__lbl">Status</span>
                        <span class="info-row__val"><span class="badge {{ $paymentStatusBadge[$order->payment_status] ?? 'badge--grey' }}">{{ ucfirst($order->payment_status) }}</span></span>
                    </div>
                    <div class="info-row">
                        <span class="info-row__lbl">Amount</span>
                        <span class="info-row__val td-gold">{{ $fmt($order->total) }} ℂ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
