@extends('layouts.storefront')

@section('title', 'My Wishlist')
@section('main-class', 'account-page')

@section('content')
<div class="container py-4 py-lg-5">
    <nav class="products-breadcrumb mb-4" aria-label="Breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.profile') }}">Account</a></li>
            <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 mb-4 mb-lg-0">
            @include('partials.account.sidebar')
        </div>
        <div class="col-lg-9">
            <h1 class="account-content__title">Wishlist</h1>

            @if ($items->isEmpty())
                <div class="card-base account-empty">
                    <p>Your wishlist is empty.</p>
                    <a href="{{ route('products.index') }}" class="btn-base btn-gold">
                        <i class="bi bi-shop" aria-hidden="true"></i> Browse the Catalogue
                    </a>
                </div>
            @else
                <div class="row g-4">
                    @foreach ($items as $wishlistItem)
                        <div class="col-sm-6 col-xl-4">
                            <x-product-card :product="$wishlistItem->product" />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
