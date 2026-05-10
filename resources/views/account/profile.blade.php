@extends('layouts.storefront')

@section('title', 'My Account')
@section('main-class', 'account-page')

@section('content')
<div class="container py-4 py-lg-5">
    <nav class="products-breadcrumb mb-4" aria-label="Breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Account</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 mb-4 mb-lg-0">
            @include('partials.account.sidebar')
        </div>
        <div class="col-lg-9">
            <div class="card-base account-info">
                <h1 class="account-info__title">Profile</h1>
                <div class="account-info__row">
                    <span class="account-info__label">Full Name</span>
                    <span class="account-info__value">{{ auth()->user()->full_name }}</span>
                </div>
                <div class="account-info__row">
                    <span class="account-info__label">Email</span>
                    <span class="account-info__value">{{ auth()->user()->email }}</span>
                </div>
                @if (auth()->user()->phone)
                    <div class="account-info__row">
                        <span class="account-info__label">Phone</span>
                        <span class="account-info__value">{{ auth()->user()->phone }}</span>
                    </div>
                @endif
                <div class="account-info__row">
                    <span class="account-info__label">Member Since</span>
                    <span class="account-info__value">{{ auth()->user()->created_at->format('F j, Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
