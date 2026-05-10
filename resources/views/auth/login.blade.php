@extends('layouts.storefront')

@section('title', 'Sign In')
@section('main-class', 'auth-page')

@section('content')
<div class="container">
    <div class="auth-card">
        <span class="eyebrow auth-card__eyebrow">Welcome back, traveller</span>
        <h1 class="auth-card__title">Sign In</h1>
        <p class="auth-card__subtitle">Access your account and order history.</p>

        <form action="{{ route('login') }}" method="POST" novalidate>
            @csrf
            <div class="form-field">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" autocomplete="email" required />
                <x-form-error field="email" />
            </div>
            <div class="form-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password" required />
                <x-form-error field="password" />
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4" style="font-size:.88rem;">
                <label class="d-flex align-items-center gap-2" style="cursor:pointer;color:var(--clr-text-muted);">
                    <input type="checkbox" name="remember" style="accent-color:var(--clr-gold);" {{ old('remember') ? 'checked' : '' }} />
                    Remember me
                </label>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" class="btn-base btn-gold btn-full">
                <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                Sign In
            </button>
        </form>

        <hr class="auth-divider" />

        <p class="auth-card__footer">
            No account yet? <a href="{{ route('register') }}">Register here</a>
        </p>
        <p class="auth-card__footer">
            <a href="{{ route('cart.show') }}">Continue as guest <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </p>
    </div>
</div>
@endsection
