@extends('layouts.storefront')

@section('title', 'Register')
@section('main-class', 'auth-page')

@section('content')
<div class="container">
    <div class="auth-card" style="max-width:540px;">
        <span class="eyebrow auth-card__eyebrow">Join the Guild</span>
        <h1 class="auth-card__title">Create Account</h1>
        <p class="auth-card__subtitle">Become a registered member of the Emporium.</p>

        <form action="{{ route('register') }}" method="POST" novalidate>
            @csrf
            <div class="form-field--row">
                <div class="form-field">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" name="first_name" value="{{ old('first_name') }}" placeholder="Geralt" autocomplete="given-name" required />
                    <x-form-error field="first_name" />
                </div>
                <div class="form-field">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" name="last_name" value="{{ old('last_name') }}" placeholder="of Rivia" autocomplete="family-name" required />
                    <x-form-error field="last_name" />
                </div>
            </div>

            <div class="form-field">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" autocomplete="email" required />
                <x-form-error field="email" />
            </div>

            <div class="form-field">
                <label for="phone">Phone (optional)</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+421 900 000 000" autocomplete="tel" />
                <x-form-error field="phone" />
            </div>

            <div class="form-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="new-password" required />
                <x-form-error field="password" />
            </div>

            <div class="form-field">
                <label for="password-confirm">Confirm Password</label>
                <input type="password" id="password-confirm" name="password_confirmation" placeholder="••••••••" autocomplete="new-password" required />
            </div>

            <div class="form-check-wwe">
                <input type="checkbox" id="terms" name="terms" {{ old('terms') ? 'checked' : '' }} required />
                <label for="terms">I agree to the <a href="#">Terms of Sale</a> and <a href="#">Privacy Policy</a> of the {{ config('app.name') }}.</label>
                <x-form-error field="terms" />
            </div>

            <div class="form-check-wwe" style="margin-top:-10px;">
                <input type="checkbox" id="newsletter" name="newsletter" {{ old('newsletter') ? 'checked' : '' }} />
                <label for="newsletter">Send me contract alerts and new stock notifications.</label>
            </div>

            <button type="submit" class="btn-base btn-gold btn-full" style="margin-top:4px;">
                <i class="bi bi-person-plus" aria-hidden="true"></i>
                Create Account
            </button>
        </form>

        <hr class="auth-divider" />

        <p class="auth-card__footer">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </p>
    </div>
</div>
@endsection
