@extends('layouts.storefront')

@section('title', 'My Orders')
@section('main-class', 'account-page')

@php
    $fmt = fn ($n) => number_format((int) $n, 0, ',', ' ');
    $statusClass = [
        'pending' => 'badge--orange',
        'processing' => 'badge--blue',
        'shipped' => 'badge--gold',
        'delivered' => 'badge--green',
        'cancelled' => 'badge--red',
        'refunded' => 'badge--grey',
    ];
@endphp

@section('content')
<div class="container py-4 py-lg-5">
    <nav class="products-breadcrumb mb-4" aria-label="Breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.profile') }}">Account</a></li>
            <li class="breadcrumb-item active" aria-current="page">Orders</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 mb-4 mb-lg-0">
            @include('partials.account.sidebar')
        </div>
        <div class="col-lg-9">
            <h1 class="account-content__title">Order History</h1>

            @if ($orders->isEmpty())
                <div class="card-base account-empty">
                    <p>You haven't placed any orders yet.</p>
                    <a href="{{ route('products.index') }}" class="btn-base btn-gold">
                        <i class="bi bi-shop" aria-hidden="true"></i> Browse the Catalogue
                    </a>
                </div>
            @else
                <div class="card-base">
                    <div class="order-list">
                        @foreach ($orders as $order)
                            <div class="order-row">
                                <span class="order-row__number">{{ $order->order_number }}</span>
                                <span class="order-row__date">{{ $order->created_at->format('M j, Y') }}</span>
                                <span class="badge {{ $statusClass[$order->status] ?? 'badge--grey' }}">{{ ucfirst($order->status) }}</span>
                                <span class="order-row__total">{{ $fmt($order->total) }} Crowns</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
