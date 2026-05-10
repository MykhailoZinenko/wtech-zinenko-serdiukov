@extends('layouts.storefront')

@section('title', 'Order Created')
@section('main-class', 'checkout-page')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
@endphp

@section('content')
<div class="container py-4 py-lg-5">
    <div class="card-base checkout-success">
        <div class="checkout-success__icon">
            <i class="bi bi-check-lg" aria-hidden="true"></i>
        </div>
        <p class="eyebrow">Order confirmed</p>
        <h1 class="checkout-title">Thank you, {{ $order->first_name }}.</h1>
        <p class="checkout-success__text">
            Your order <strong>{{ $order->number }}</strong> was created successfully.
        </p>

        <div class="checkout-success__grid">
            <div>
                <span class="eyebrow">Delivery</span>
                <p>{{ $deliveryOptions[$order->delivery_method]['label'] ?? $order->delivery_method }}</p>
            </div>
            <div>
                <span class="eyebrow">Payment</span>
                <p>{{ $paymentOptions[$order->payment_method]['label'] ?? $order->payment_method }}</p>
            </div>
            <div>
                <span class="eyebrow">Total</span>
                <p>{{ $fmt($order->total) }} Crowns</p>
            </div>
        </div>

        <div class="checkout-summary__items checkout-success__items">
            @foreach ($order->items as $item)
                <div class="checkout-summary__item">
                    <div>
                        <span class="checkout-summary__item-name">{{ $item->product_name }} x{{ $item->quantity }}</span>
                        @if ($item->sku)
                            <span class="checkout-success__sku">{{ $item->sku }}</span>
                        @endif
                    </div>
                    <span class="checkout-summary__item-price">{{ $fmt($item->line_total) }} Cr</span>
                </div>
            @endforeach
        </div>

        <div class="checkout-success__actions">
            <a href="{{ route('products.index') }}" class="btn-base btn-gold">Continue Shopping</a>
            <a href="{{ route('cart.show') }}" class="btn-base btn-outline-gold">Back to Cart</a>
        </div>
    </div>
</div>
@endsection
