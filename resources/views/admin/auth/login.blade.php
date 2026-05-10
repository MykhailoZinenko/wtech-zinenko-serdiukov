@extends('layouts.auth-admin')

@section('title', 'Admin Login')

@section('content')
<div class="login-card">
    <div class="login-card__top">
        <div class="login-card__icon"><i class="bi bi-shield-lock-fill"></i></div>
        <div class="login-card__title">Admin Access</div>
        <div class="login-card__sub">White Wolf Emporium — Control Panel</div>
    </div>

    <div class="login-card__body">
        @if ($errors->any())
            <div class="form-alert form-alert--error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ url('admin/login') }}" method="POST" novalidate>
            @csrf

            <div class="form-field">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@whitewolf.nv" autocomplete="username" required />
                <x-form-error field="email" />
            </div>

            <div class="form-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password" required />
                <x-form-error field="password" />
            </div>

            <div class="login-card__options">
                <label class="form-check-wwe">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} />
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn-base btn-gold btn-full">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </button>
        </form>

        <a href="{{ route('home') }}" class="login-back">
            <i class="bi bi-arrow-left"></i> Back to storefront
        </a>
    </div>
</div>
@endsection
