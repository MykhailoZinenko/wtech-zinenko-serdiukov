@extends('layouts.storefront')

@section('title', 'Dashboard')
@section('main-class', 'auth-page')

@section('content')
<div class="container">
    <div class="auth-card" style="max-width:600px;">
        <span class="eyebrow auth-card__eyebrow">Your Account</span>
        <h1 class="auth-card__title">Dashboard</h1>
        <p class="auth-card__subtitle">Welcome back, {{ auth()->user()->first_name }}.</p>

        <div class="form-field">
            <label>Name</label>
            <p style="color:var(--clr-text);margin:4px 0 0;">{{ auth()->user()->full_name }}</p>
        </div>
        <div class="form-field">
            <label>Email</label>
            <p style="color:var(--clr-text);margin:4px 0 0;">{{ auth()->user()->email }}</p>
        </div>
        @if(auth()->user()->phone)
        <div class="form-field">
            <label>Phone</label>
            <p style="color:var(--clr-text);margin:4px 0 0;">{{ auth()->user()->phone }}</p>
        </div>
        @endif
        <div class="form-field">
            <label>Member Since</label>
            <p style="color:var(--clr-text);margin:4px 0 0;">{{ auth()->user()->created_at->format('F j, Y') }}</p>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-base btn-gold btn-full">
                <i class="bi bi-box-arrow-left" aria-hidden="true"></i> Sign Out
            </button>
        </form>
    </div>
</div>
@endsection
