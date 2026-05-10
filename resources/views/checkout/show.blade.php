@extends('layouts.storefront')

@section('title', 'Checkout')
@section('main-class', 'checkout-page')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $selectedShipping = $shippingMethods->firstWhere('id', old('shipping_method_id', $selectedShippingMethodId)) ?? $shippingMethods->first();
    $selectedPayment = $paymentMethods->firstWhere('id', old('payment_method_id', $selectedPaymentMethodId)) ?? $paymentMethods->first();
    $currentShippingCost = $selectedShipping?->cost ?? 0;
    $currentTotal = $subtotal + $currentShippingCost;
    $shippingCosts = $shippingMethods->pluck('cost', 'id')->all();
    $user = auth()->user();
@endphp

@section('content')
<div class="container py-4 py-lg-5">
    <nav aria-label="Breadcrumb" class="products-breadcrumb mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
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
        data-shipping-costs='@json($shippingCosts)'
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
                        Shipping Method
                    </h2>
                    <div class="shipping-options" role="radiogroup" aria-label="Select delivery method">
                        @foreach ($shippingMethods as $method)
                            <label class="shipping-option">
                                <input type="radio" name="shipping_method_id" value="{{ $method->id }}" @checked(old('shipping_method_id', $selectedShippingMethodId) == $method->id) />
                                <div class="shipping-option__info">
                                    <span class="shipping-option__name">{{ $method->name }}</span>
                                    <span class="shipping-option__desc">{{ $method->description ?? 'Est. ' . $method->estimated_days . ' days' }}</span>
                                </div>
                                <span class="shipping-option__price">{{ $method->cost === 0 ? 'Free' : $fmt($method->cost) . ' Crowns' }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('shipping_method_id') <p class="form-error">{{ $message }}</p> @enderror
                </section>

                <section class="card-base checkout-section" aria-labelledby="payment-title">
                    <h2 class="eyebrow checkout-section__title" id="payment-title">
                        <i class="bi bi-cash-coin" aria-hidden="true"></i>
                        Payment Method
                    </h2>
                    <div class="payment-options" role="radiogroup" aria-label="Select payment method">
                        @foreach ($paymentMethods as $method)
                            <label class="payment-option">
                                <input type="radio" name="payment_method_id" value="{{ $method->id }}" @checked(old('payment_method_id', $selectedPaymentMethodId) == $method->id) />
                                <div class="payment-option__info">
                                    <span class="payment-option__name">{{ $method->name }}</span>
                                    <span class="payment-option__desc">{{ $method->description ?? '' }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('payment_method_id') <p class="form-error">{{ $message }}</p> @enderror
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
                                <span class="checkout-summary__item-name">{{ $product?->name ?? 'Unavailable product' }} &times;{{ $item->quantity }}</span>
                                <span class="checkout-summary__item-price">{{ number_format($item->lineTotal, 0, ',', ' ') }} Cr</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="checkout-summary__row">
                        <span>Subtotal</span>
                        <span>{{ $fmt($subtotal) }} Crowns</span>
                    </div>
                    <div class="checkout-summary__row">
                        <span>Shipping</span>
                        <span data-checkout-shipping>{{ $currentShippingCost === 0 ? 'Free' : $fmt($currentShippingCost) . ' Crowns' }}</span>
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
