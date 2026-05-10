@extends('layouts.storefront')

@section('title', 'Checkout')
@section('main-class', 'checkout-page')

@php
    $items = $cart->items;
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $currentDelivery = old('delivery', $selectedDelivery);
    $currentPayment = old('payment', $selectedPayment);
    $currentDelivery = array_key_exists($currentDelivery, $deliveryOptions) ? $currentDelivery : $selectedDelivery;
    $currentPayment = array_key_exists($currentPayment, $paymentOptions) ? $currentPayment : $selectedPayment;
    $currentDeliveryFee = $currentDelivery === 'courier' && $subtotal >= 500 ? 0 : $deliveryOptions[$currentDelivery]['fee'];
    $currentPaymentFee = $paymentOptions[$currentPayment]['fee'];
    $currentTotal = $subtotal + $currentDeliveryFee + $currentPaymentFee;
    $deliveryFees = collect($deliveryOptions)
        ->map(fn ($option, $key) => $key === 'courier' && $subtotal >= 500 ? 0 : $option['fee'])
        ->all();
    $paymentFees = collect($paymentOptions)->map(fn ($option) => $option['fee'])->all();
    $user = auth()->user();
@endphp

@push('styles')
<style>
    .checkout-page .checkout-section {
        padding: 24px;
        margin-bottom: 24px;
    }

    .checkout-page .shipping-options,
    .checkout-page .payment-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .checkout-page .shipping-option,
    .checkout-page .payment-option {
        display: flex;
        align-items: center;
        gap: 14px;
        width: 100%;
        padding: 14px 18px;
        background: var(--clr-bg-2);
        border: 1px solid var(--clr-border);
        border-radius: var(--radius-sm);
    }

    .checkout-page .shipping-option input,
    .checkout-page .payment-option input {
        width: 16px;
        height: 16px;
        flex: 0 0 16px;
        margin: 0;
        accent-color: var(--clr-gold);
    }

    .checkout-page .shipping-option__info,
    .checkout-page .payment-option__info {
        flex: 1 1 auto;
        min-width: 0;
    }

    .checkout-page .shipping-option__name,
    .checkout-page .payment-option__name,
    .checkout-page .shipping-option__desc,
    .checkout-page .payment-option__desc {
        display: block;
    }

    .checkout-page .shipping-option__price {
        flex: 0 0 auto;
        white-space: nowrap;
    }

    .checkout-page .checkout-summary__items {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 16px;
    }

    .checkout-page .checkout-summary__item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .checkout-page .checkout-summary__item-img {
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        overflow: hidden;
        border-radius: var(--radius-sm);
    }

    .checkout-page .checkout-summary__item-img img,
    .checkout-page .checkout-summary__item-img .placeholder-asset {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .checkout-page .checkout-summary__item-name {
        flex: 1 1 auto;
        min-width: 0;
    }
</style>
@endpush

@section('content')
<div class="container py-4 py-lg-5">
    <nav aria-label="Breadcrumb" class="products-breadcrumb mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Shop</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart.show') }}">Cart</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>

    <h1 class="checkout-title">Checkout</h1>

    <form
        action="{{ route('checkout.store') }}"
        method="POST"
        novalidate
        data-checkout-form
        data-subtotal="{{ $subtotal }}"
        data-delivery-fees='@json($deliveryFees)'
        data-payment-fees='@json($paymentFees)'
    >
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <section class="card-base checkout-section" aria-labelledby="delivery-title">
                    <h2 class="eyebrow checkout-section__title" id="delivery-title">
                        <i class="bi bi-person-lines-fill" aria-hidden="true"></i>
                        Delivery Details
                    </h2>

                    <div class="form-field--row">
                        <div class="form-field">
                            <label for="co-first-name">First Name</label>
                            <input type="text" id="co-first-name" name="first_name" value="{{ old('first_name', $user?->first_name) }}" placeholder="Geralt" autocomplete="given-name" required />
                            @error('first_name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-field">
                            <label for="co-last-name">Last Name</label>
                            <input type="text" id="co-last-name" name="last_name" value="{{ old('last_name', $user?->last_name) }}" placeholder="of Rivia" autocomplete="family-name" required />
                            @error('last_name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="co-email">Email Address</label>
                        <input type="email" id="co-email" name="email" value="{{ old('email', $user?->email) }}" placeholder="your@email.com" autocomplete="email" required />
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-field">
                        <label for="co-phone">Phone Number</label>
                        <input type="tel" id="co-phone" name="phone" value="{{ old('phone', $user?->phone) }}" placeholder="+421 900 000 000" autocomplete="tel" />
                        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-field">
                        <label for="co-address">Street Address</label>
                        <input type="text" id="co-address" name="address" value="{{ old('address') }}" placeholder="7 Hierarch Square" autocomplete="street-address" required />
                        @error('address') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-field--row">
                        <div class="form-field">
                            <label for="co-city">City</label>
                            <input type="text" id="co-city" name="city" value="{{ old('city') }}" placeholder="Novigrad" autocomplete="address-level2" required />
                            @error('city') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-field">
                            <label for="co-postal">Postal Code</label>
                            <input type="text" id="co-postal" name="postal_code" value="{{ old('postal_code') }}" placeholder="10100" autocomplete="postal-code" required />
                            @error('postal_code') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="co-region">Region / Kingdom</label>
                        <select id="co-region" name="region" autocomplete="address-level1" required>
                            <option value="">Select a region...</option>
                            <option value="novigrad" @selected(old('region') === 'novigrad')>Free City of Novigrad</option>
                            <option value="redania" @selected(old('region') === 'redania')>Kingdom of Redania</option>
                            <option value="temeria" @selected(old('region') === 'temeria')>Kingdom of Temeria</option>
                            <option value="nilfgaard" @selected(old('region') === 'nilfgaard')>Nilfgaardian Empire</option>
                            <option value="skellige" @selected(old('region') === 'skellige')>Skellige Isles</option>
                            <option value="velen" @selected(old('region') === 'velen')>Velen</option>
                            <option value="toussaint" @selected(old('region') === 'toussaint')>Duchy of Toussaint</option>
                            <option value="other" @selected(old('region') === 'other')>Other</option>
                        </select>
                        @error('region') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-field">
                        <label for="co-notes">Order Notes (optional)</label>
                        <textarea id="co-notes" name="notes" rows="3" placeholder="Special delivery instructions, contract notes...">{{ old('notes') }}</textarea>
                        @error('notes') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </section>

                <section class="card-base checkout-section" aria-labelledby="shipping-title">
                    <h2 class="eyebrow checkout-section__title" id="shipping-title">
                        <i class="bi bi-truck" aria-hidden="true"></i>
                        Delivery Method
                    </h2>
                    <div class="shipping-options" role="radiogroup" aria-label="Select delivery method">
                        @foreach ($deliveryOptions as $key => $option)
                            @php($fee = $key === 'courier' && $subtotal >= 500 ? 0 : $option['fee'])
                            <label class="shipping-option">
                                <input type="radio" name="delivery" value="{{ $key }}" @checked($currentDelivery === $key) />
                                <div class="shipping-option__info">
                                    <span class="shipping-option__name">{{ $option['label'] }}</span>
                                    <span class="shipping-option__desc">{{ $option['description'] }}</span>
                                </div>
                                <span class="shipping-option__price">{{ $fee === 0 ? 'Free' : $fmt($fee) . ' Crowns' }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('delivery') <p class="form-error">{{ $message }}</p> @enderror
                </section>

                <section class="card-base checkout-section" aria-labelledby="payment-title">
                    <h2 class="eyebrow checkout-section__title" id="payment-title">
                        <i class="bi bi-cash-coin" aria-hidden="true"></i>
                        Payment Method
                    </h2>
                    <div class="payment-options" role="radiogroup" aria-label="Select payment method">
                        @foreach ($paymentOptions as $key => $option)
                            <label class="payment-option">
                                <input type="radio" name="payment" value="{{ $key }}" @checked($currentPayment === $key) />
                                <div class="payment-option__info">
                                    <span class="payment-option__name">{{ $option['label'] }}</span>
                                    <span class="payment-option__desc">{{ $option['description'] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('payment') <p class="form-error">{{ $message }}</p> @enderror
                </section>
            </div>

            <div class="col-lg-4">
                <aside class="card-base sticky-sidebar checkout-summary" aria-label="Order summary">
                    <h2 class="card-title card-title--bordered">Order Summary</h2>

                    <div class="checkout-summary__items">
                        @foreach ($items as $item)
                            @php($product = $item->product)
                            <div class="checkout-summary__item">
                                <div class="checkout-summary__item-img">
                                    @if ($product?->display_image_url)
                                        <img src="{{ $product->display_image_url }}" alt="{{ $product->name }}" />
                                    @else
                                        <div class="placeholder-asset">IMG</div>
                                    @endif
                                </div>
                                <span class="checkout-summary__item-name">{{ $product?->name ?? 'Unavailable product' }} x{{ $item->quantity }}</span>
                                <span class="checkout-summary__item-price">{{ $item->formatted_line_total }} Cr</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="checkout-summary__row">
                        <span>Subtotal</span>
                        <span>{{ $fmt($subtotal) }} Crowns</span>
                    </div>
                    <div class="checkout-summary__row">
                        <span>Delivery</span>
                        <span data-checkout-delivery>{{ $currentDeliveryFee === 0 ? 'Free' : $fmt($currentDeliveryFee) . ' Crowns' }}</span>
                    </div>
                    <div class="checkout-summary__row">
                        <span>Payment</span>
                        <span data-checkout-payment>{{ $currentPaymentFee === 0 ? 'Free' : $fmt($currentPaymentFee) . ' Crowns' }}</span>
                    </div>
                    <div class="checkout-summary__row checkout-summary__total">
                        <span>Total</span>
                        <span data-checkout-total>{{ $fmt($currentTotal) }} Crowns</span>
                    </div>

                    <button type="submit" class="btn-base btn-gold btn-full checkout-submit">
                        <i class="bi bi-check-circle" aria-hidden="true"></i>
                        Place Order
                    </button>

                    <p class="checkout-secure-note">
                        <i class="bi bi-shield-lock" aria-hidden="true"></i>
                        Secured by Mage-Level Encryption
                    </p>
                </aside>
            </div>
        </div>
    </form>
</div>
@endsection
