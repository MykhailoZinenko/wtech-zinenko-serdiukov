@extends('layouts.storefront')

@section('title', $title)
@section('main-class', 'info-page')

@section('content')
<div class="container py-4 py-lg-5">
    <nav class="products-breadcrumb mb-4" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $heading }}</li>
        </ol>
    </nav>

    <article class="card-base info-card">
        <p class="eyebrow info-eyebrow">{{ $eyebrow }}</p>
        <h1 class="info-title">{{ $heading }}</h1>
        <p class="info-lead">{{ $lead }}</p>

        <div class="info-sections">
            @foreach ($sections as $section)
                <section class="info-section">
                    <h2>{{ $section['title'] }}</h2>
                    <p>{{ $section['body'] }}</p>
                </section>
            @endforeach
        </div>

        <div class="info-actions">
            <a href="{{ route('products.index') }}" class="btn-base btn-gold">Browse Catalogue</a>
            <a href="{{ route('cart.show') }}" class="btn-base btn-outline-gold">View Cart</a>
        </div>
    </article>
</div>
@endsection
