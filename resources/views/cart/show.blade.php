@extends('layouts.storefront')

@section('title', 'Your Cart')
@section('main-class', 'cart-page')

@php
    $itemCount = $items->sum('quantity');
    $countLabel = $itemCount === 1 ? '1 item' : "{$itemCount} items";
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
@endphp

@section('content')
<div class="container py-4 py-lg-5">

    <nav class="products-breadcrumb mb-4" aria-label="Breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Your Cart</li>
        </ol>
    </nav>

    <h1 class="cart-title">Your Cart</h1>
    <p class="cart-count-text mb-4">{{ $countLabel }}</p>

    @if (session('cart_success'))
        <div class="form-alert form-alert--success">{{ session('cart_success') }}</div>
    @endif
    @if (session('cart_error'))
        <div class="form-alert form-alert--error">{{ session('cart_error') }}</div>
    @endif

    @if ($items->isEmpty())
        <div class="card-base cart-empty">
            <p>Your cart is empty.</p>
            <a href="{{ route('products.index') }}" class="btn-base btn-gold">
                <i class="bi bi-bag-plus"></i> Browse the Catalogue
            </a>
        </div>
    @else
        <div class="row">
            <div class="col-lg-8">
                <div class="cart-items">
                    @foreach ($items as $item)
                        @php
                            $product = $item->product;
                            $image = $product?->display_image_url;
                        @endphp
                        <article class="cart-item">
                            <a href="{{ $product ? route('products.show', $product) : '#' }}" class="cart-item__img">
                                @if ($image)
                                    <img src="{{ $image }}" alt="{{ $product?->name }}" />
                                @else
                                    <div class="placeholder-asset">IMAGE: {{ $product?->name }}</div>
                                @endif
                            </a>
                            <div class="cart-item__body">
                                <h3 class="cart-item__name"><a href="{{ $product ? route('products.show', $product) : '#' }}">{{ $product?->name ?? 'Unavailable product' }}</a></h3>
                                <p class="cart-item__meta">{{ $product?->category?->name }}</p>
                                <div class="cart-item__price">{{ number_format($item->product->price, 0, ',', ' ') }} <span class="eyebrow product-price__unit">Crowns</span></div>
                            </div>
                            <div class="cart-item__qty">
                                <form action="{{ route('cart.update', $item) }}" method="POST" data-cart-update>
                                    @csrf
                                    @method('PATCH')
                                    <label for="cart-qty-{{ $item->id }}" class="visually-hidden">Quantity</label>
                                    <input type="number" id="cart-qty-{{ $item->id }}" name="quantity" class="input-base cart-item__qty-input" min="1" max="99" value="{{ $item->quantity }}" onchange="this.form.submit()" />
                                </form>
                            </div>
                            <div class="cart-item__total">{{ number_format($item->lineTotal, 0, ',', ' ') }} <span class="eyebrow product-price__unit">Crowns</span></div>
                            <form action="{{ route('cart.remove', $item) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="cart-item__remove" aria-label="Remove {{ $product?->name }} from cart">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0">
                <aside class="card-base sticky-sidebar cart-summary">
                    <h2 class="card-title">Order Summary</h2>
                    <div class="cart-summary__row">
                        <span>Subtotal</span>
                        <span>{{ $fmt($subtotal) }} <span class="eyebrow product-price__unit">Crowns</span></span>
                    </div>
                    <div class="cart-summary__row">
                        <span>Delivery</span>
                        <span>{{ $subtotal >= 500 ? 'Free' : 'Calculated at checkout' }}</span>
                    </div>
                    <div class="cart-summary__row cart-summary__total">
                        <span>Total</span>
                        <span>{{ $fmt($subtotal) }} <span class="eyebrow product-price__unit">Crowns</span></span>
                    </div>
                    <a href="{{ route('checkout.show') }}" class="btn-base btn-gold btn-full" style="margin-top:20px;">
                        <i class="bi bi-lock" aria-hidden="true"></i> Proceed to Checkout
                    </a>
                    <a href="{{ route('products.index') }}" class="cart-summary__continue">Continue shopping</a>
                </aside>
            </div>
        </div>
    @endif
</div>
@endsection
