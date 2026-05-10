@extends('layouts.storefront')

@section('title', $category?->name ?? ($searchTerm ? 'Search: ' . $searchTerm : 'Shop'))
@section('main-class', 'products-page')

@php
    $hiddenParams = collect(request()->query())->except(['sort', 'page']);
    $sortHiddenParams = collect(request()->query())->except(['sort', 'page']);
    $heading = $category?->name ?? ($searchTerm ? "Search results for \"{$searchTerm}\"" : 'All Products');
    $totalLabel = $products->total() === 1 ? '1 product' : "{$products->total()} products";
    $rangeStart = $products->firstItem();
    $rangeEnd = $products->lastItem();
@endphp

@section('content')
<div class="container py-4 py-lg-5">

    <x-category-breadcrumb :category="$category" />

    <header class="products-header mb-4">
        <div class="products-header__main">
            <div>
                <h1 class="products-title">{{ $heading }}</h1>
                <p class="products-count">
                    @if ($products->total() > 0)
                        Showing {{ $rangeStart }}–{{ $rangeEnd }} of {{ $totalLabel }}
                    @else
                        No products match your filters
                    @endif
                </p>
            </div>
            <form class="products-sort" method="get">
                @foreach ($sortHiddenParams as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                @endforeach
                <label for="sort-select" class="sort-label">Sort by</label>
                <select id="sort-select" name="sort" class="input-base select-arrow sort-select" onchange="this.form.submit()">
                    <option value="">Relevance</option>
                    <option value="price_asc" @selected($sort === 'price_asc')>Price: Low to High</option>
                    <option value="price_desc" @selected($sort === 'price_desc')>Price: High to Low</option>
                    <option value="name_asc" @selected($sort === 'name_asc')>Name: A–Z</option>
                    <option value="name_desc" @selected($sort === 'name_desc')>Name: Z–A</option>
                    <option value="newest" @selected($sort === 'newest')>Newest First</option>
                </select>
            </form>
        </div>
    </header>

    <div class="row">
        <aside class="col-lg-3 mb-4 mb-lg-0">
            <div class="card-base sticky-sidebar products-filters">
                <h2 class="eyebrow filters-title">Filters</h2>
                <form class="filters-form" method="get" id="filtersForm">
                    @if ($searchTerm)
                        <input type="hidden" name="q" value="{{ $searchTerm }}" />
                    @endif
                    @if ($sort)
                        <input type="hidden" name="sort" value="{{ $sort }}" />
                    @endif

                    <div class="filter-group">
                        <label class="eyebrow filter-label">Price (Crowns)</label>
                        <div class="filter-price-row">
                            <input type="number" name="min_price" id="min_price" placeholder="Min" min="0" step="10" class="input-base filter-input" value="{{ $filters['min_price'] }}" />
                            <span class="filter-sep">–</span>
                            <input type="number" name="max_price" id="max_price" placeholder="Max" min="0" step="10" class="input-base filter-input" value="{{ $filters['max_price'] }}" />
                        </div>
                        <div class="filter-price-slider">
                            <input type="range" id="price-slider-min" class="filter-range" min="0" max="10000" value="{{ $filters['min_price'] ?: 0 }}" step="50" title="Min price" />
                            <input type="range" id="price-slider-max" class="filter-range" min="0" max="10000" value="{{ $filters['max_price'] ?: 10000 }}" step="50" title="Max price" />
                        </div>
                    </div>

                    <div class="filter-group">
                        <label class="eyebrow filter-label">School / Brand</label>
                        <select name="school" class="input-base select-arrow filter-select">
                            <option value="">All</option>
                            <option value="wolf" @selected($filters['school'] === 'wolf')>School of the Wolf</option>
                            <option value="griffin" @selected($filters['school'] === 'griffin')>School of the Griffin</option>
                            <option value="bear" @selected($filters['school'] === 'bear')>School of the Bear</option>
                            <option value="cat" @selected($filters['school'] === 'cat')>School of the Cat</option>
                            <option value="manticore" @selected($filters['school'] === 'manticore')>School of the Manticore</option>
                            <option value="viper" @selected($filters['school'] === 'viper')>School of the Viper</option>
                            <option value="generic" @selected($filters['school'] === 'generic')>Generic</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="eyebrow filter-label">Rarity</label>
                        <select name="rarity" class="input-base select-arrow filter-select">
                            <option value="">All</option>
                            <option value="common" @selected($filters['rarity'] === 'common')>Common</option>
                            <option value="uncommon" @selected($filters['rarity'] === 'uncommon')>Uncommon</option>
                            <option value="rare" @selected($filters['rarity'] === 'rare')>Rare</option>
                            <option value="legendary" @selected($filters['rarity'] === 'legendary')>Legendary</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-base btn-gold btn-sm">Apply</button>
                        <a href="{{ $category ? route('products.category', ['categorySlug' => $category->slug]) : route('products.index') }}" class="filter-reset">Reset</a>
                    </div>
                </form>
            </div>
        </aside>

        <div class="col-lg-9">
            @if (session('cart_success'))
                <div class="cart-flash">{{ session('cart_success') }}</div>
            @endif

            @if ($products->isEmpty())
                <div class="card-base products-empty">
                    <p>No products match your filters. Try widening your price range or clearing the filters.</p>
                </div>
            @else
                <div class="row g-4 products-grid">
                    @foreach ($products as $product)
                        <div class="col-sm-6 col-xl-4">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>

                {{ $products->links('vendor.pagination.products') }}
            @endif
        </div>
    </div>
</div>
@endsection
