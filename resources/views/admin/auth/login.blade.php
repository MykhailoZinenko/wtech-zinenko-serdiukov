@extends('layouts.auth-admin')

@section('title', 'Admin Login')

@section('content')
<div class="login-card">
    <div class="login-card__top">
        <div class="login-card__icon"><i class="bi bi-shield-lock-fill"></i></div>
        <div class="login-card__title">Admin Access</div>
        <div class="login-card__sub">{{ config('app.name') }} &mdash; Control Panel</div>
    </div>
    <div class="login-card__body">
        <form action="{{ route('admin.login') }}" method="POST" novalidate>
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
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;margin-top:2px;line-height:1;">
                <label class="form-check-wwe" style="margin:0;line-height:1;font-size:.82rem;">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} />
                    Remember me
                </label>
            </div>
            <button type="submit" class="btn-base btn-gold" style="width:100%;">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </button>
        </form>
        <a href="{{ url('/') }}" class="login-back">
            <i class="bi bi-arrow-left"></i> Back to storefront
        </a>
    </div>
</div>
@endsection
