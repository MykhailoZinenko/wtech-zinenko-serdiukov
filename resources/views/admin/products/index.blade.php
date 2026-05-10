@extends('layouts.admin')

@section('title', 'Products')

@php
    $fmt = fn ($n) => number_format((int) $n, 0, ',', ' ');
    $rarityBadge = [
        'common' => 'badge--grey',
        'new' => 'badge--grey',
        'limited' => 'badge--blue',
        'rare' => 'badge--orange',
        'legendary' => 'badge--gold',
    ];
    $schoolLabels = [
        'wolf' => 'Wolf School', 'griffin' => 'Griffin School', 'cat' => 'Cat School',
        'bear' => 'Bear School', 'viper' => 'Viper School', 'manticore' => 'Manticore School',
        'ofieri' => 'Ofieri', 'toussaint' => 'Toussaint', 'none' => '—',
    ];
@endphp

@section('content')
<div class="page-hdr">
    <div class="page-hdr__left">
        <h1 class="page-hdr__title">Products</h1>
        <p class="page-hdr__sub">{{ $fmt($stats['total']) }} products · {{ $lowStockCount }} low stock · {{ $limitedCount }} limited edition</p>
    </div>
    <div class="page-hdr__actions">
        <a href="{{ route('admin.products.create') }}" class="btn-base btn-gold"><i class="bi bi-plus-lg"></i> Add Product</a>
    </div>
</div>

@if (session('admin_success'))
    <div class="form-alert form-alert--success">{{ session('admin_success') }}</div>
@endif

<div class="stats-grid">
    <div class="stat-card stat-card--gold">
        <div class="stat-card__icon"><i class="bi bi-box-seam"></i></div>
        <div class="stat-card__label">Total Products</div>
        <div class="stat-card__value">{{ $fmt($stats['total']) }}</div>
    </div>
    <div class="stat-card stat-card--blue">
        <div class="stat-card__icon"><i class="bi bi-tags"></i></div>
        <div class="stat-card__label">Categories</div>
        <div class="stat-card__value">{{ $fmt($stats['categories']) }}</div>
    </div>
    <div class="stat-card stat-card--red">
        <div class="stat-card__icon"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="stat-card__label">Out of Stock</div>
        <div class="stat-card__value">{{ $fmt($stats['outOfStock']) }}</div>
    </div>
    <div class="stat-card stat-card--green">
        <div class="stat-card__icon"><i class="bi bi-star"></i></div>
        <div class="stat-card__label">Limited Items</div>
        <div class="stat-card__value">{{ $fmt($stats['limited']) }}</div>
    </div>
</div>

<div class="adm-card">
    <form class="tbl-toolbar" method="GET">
        <div class="tbl-search">
            <i class="bi bi-search"></i>
            <input type="search" name="q" value="{{ $filters['search'] }}" placeholder="Search by name or SKU…" />
        </div>
        <select name="category_id" class="input-base select-arrow select-input" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) $filters['categoryId'] === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="rarity" class="input-base select-arrow select-input" onchange="this.form.submit()">
            <option value="">All Rarities</option>
            <option value="common" @selected($filters['rarity'] === 'common')>Common</option>
            <option value="new" @selected($filters['rarity'] === 'new')>New</option>
            <option value="limited" @selected($filters['rarity'] === 'limited')>Limited</option>
            <option value="rare" @selected($filters['rarity'] === 'rare')>Rare</option>
            <option value="legendary" @selected($filters['rarity'] === 'legendary')>Legendary</option>
        </select>
        <select name="sort" class="input-base select-arrow select-input ms-auto" onchange="this.form.submit()">
            <option value="newest" @selected($filters['sort'] === 'newest')>Sort: Newest</option>
            <option value="name_asc" @selected($filters['sort'] === 'name_asc')>Sort: Name A–Z</option>
            <option value="price_asc" @selected($filters['sort'] === 'price_asc')>Sort: Price ↑</option>
            <option value="price_desc" @selected($filters['sort'] === 'price_desc')>Sort: Price ↓</option>
            <option value="stock_asc" @selected($filters['sort'] === 'stock_asc')>Sort: Stock ↑</option>
        </select>
    </form>

    <div class="tbl-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="col-hide-md">Category</th>
                    <th class="col-hide-md">Price</th>
                    <th class="col-hide-sm">Stock</th>
                    <th class="col-hide-sm">Rarity</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            <div class="tbl-product">
                                <div class="tbl-thumb">
                                    @if ($product->display_image_url)
                                        <img src="{{ $product->display_image_url }}" alt="{{ $product->name }}" />
                                    @else
                                        <div class="placeholder-asset">IMG</div>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="tbl-product__name tbl-link">{{ $product->name }}</a>
                                    <span class="tbl-product__cat">{{ $schoolLabels[$product->school] ?? $product->school }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="col-hide-md td-dim">{{ $product->category?->name }}</td>
                        <td class="col-hide-md td-gold">{{ $fmt($product->price) }} ℂ</td>
                        <td class="col-hide-sm">
                            <span class="{{ $product->stock === 0 ? 'stock-none' : ($product->stock <= $product->low_stock_threshold ? 'stock-low' : 'stock-ok') }} td-heading">{{ $product->stock }}</span>
                        </td>
                        <td class="col-hide-sm"><span class="badge {{ $rarityBadge[$product->rarity] ?? 'badge--grey' }}">{{ ucfirst($product->rarity) }}</span></td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn-base btn-outline-gold btn-sm btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-base btn-red btn-sm btn-icon" title="Delete" data-confirm="Permanently delete {{ $product->name }}?"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="td-dim" style="text-align:center;padding:40px;">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($products->hasPages())
        <div class="pagination-bar">
            <span>Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $fmt($products->total()) }} products</span>
            <div class="pagination-btns">
                @if ($products->onFirstPage())
                    <span class="pg-disabled"><i class="bi bi-chevron-left"></i></span>
                @else
                    <a href="{{ $products->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a>
                @endif

                @foreach ($products->getUrlRange(max(1, $products->currentPage() - 2), min($products->lastPage(), $products->currentPage() + 2)) as $page => $url)
                    @if ($page === $products->currentPage())
                        <span class="pg-active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($products->currentPage() + 2 < $products->lastPage())
                    <span class="pg-ellipsis">…</span>
                    <a href="{{ $products->url($products->lastPage()) }}">{{ $products->lastPage() }}</a>
                @endif

                @if ($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a>
                @else
                    <span class="pg-disabled"><i class="bi bi-chevron-right"></i></span>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
