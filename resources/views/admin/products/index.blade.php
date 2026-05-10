@extends('layouts.admin')

@section('title', 'Products')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $statuses = ['active' => 'Active', 'draft' => 'Draft', 'archived' => 'Archived', 'out_of_stock' => 'Out of stock'];
    $rarities = ['common' => 'Common', 'uncommon' => 'Uncommon', 'rare' => 'Rare', 'legendary' => 'Legendary'];
@endphp

@section('content')
<div class="page-hdr">
    <div class="page-hdr__left">
        <h1 class="page-hdr__title">Products</h1>
        <p class="page-hdr__sub">{{ $stats['total'] }} products · {{ $stats['outOfStock'] }} out of stock · {{ $stats['featured'] }} featured</p>
    </div>
    <div class="page-hdr__actions">
        <a href="{{ route('admin.products.create') }}" class="btn-base btn-gold"><i class="bi bi-plus-lg"></i> Add Product</a>
    </div>
</div>

@if (session('admin_success'))
    <div class="admin-flash">{{ session('admin_success') }}</div>
@endif

<div class="stats-grid">
    <div class="stat-card stat-card--gold">
        <div class="stat-card__icon"><i class="bi bi-box-seam"></i></div>
        <div class="stat-card__label">Total Products</div>
        <div class="stat-card__value">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-card stat-card--blue">
        <div class="stat-card__icon"><i class="bi bi-tags"></i></div>
        <div class="stat-card__label">Categories</div>
        <div class="stat-card__value">{{ $stats['categories'] }}</div>
    </div>
    <div class="stat-card stat-card--red">
        <div class="stat-card__icon"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="stat-card__label">Out of Stock</div>
        <div class="stat-card__value">{{ $stats['outOfStock'] }}</div>
    </div>
    <div class="stat-card stat-card--green">
        <div class="stat-card__icon"><i class="bi bi-star"></i></div>
        <div class="stat-card__label">Featured</div>
        <div class="stat-card__value">{{ $stats['featured'] }}</div>
    </div>
</div>

<div class="adm-card">
    <form class="tbl-toolbar" method="GET">
        <div class="tbl-search">
            <i class="bi bi-search"></i>
            <input type="search" name="q" value="{{ $filters['search'] }}" placeholder="Search by name or SKU..." />
        </div>
        <select name="category_id" class="input-base select-arrow select-input">
            <option value="">All Categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) $filters['categoryId'] === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="status" class="input-base select-arrow select-input">
            <option value="">All Statuses</option>
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="rarity" class="input-base select-arrow select-input">
            <option value="">All Rarities</option>
            @foreach ($rarities as $key => $label)
                <option value="{{ $key }}" @selected($filters['rarity'] === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn-base btn-outline-gold btn-sm" type="submit"><i class="bi bi-funnel"></i> Filter</button>
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
                                    <span class="tbl-product__cat">{{ $product->sku }} · {{ $statuses[$product->status] ?? $product->status }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="col-hide-md td-dim">{{ $product->category?->name }}</td>
                        <td class="col-hide-md td-gold">{{ $fmt($product->price) }} Cr</td>
                        <td class="col-hide-sm">
                            <span class="{{ $product->stock_quantity === 0 ? 'stock-none' : ($product->stock_quantity <= $product->low_stock_threshold ? 'stock-low' : 'stock-ok') }} td-heading">{{ $product->stock_quantity }}</span>
                        </td>
                        <td class="col-hide-sm"><span class="badge badge--{{ $product->rarity === 'legendary' ? 'gold' : ($product->rarity === 'rare' ? 'orange' : 'grey') }}">{{ $rarities[$product->rarity] ?? $product->rarity }}</span></td>
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
                        <td colspan="6" class="td-dim">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($products->hasPages())
        <div class="pagination-bar">
            <span>Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }}</span>
            <div class="pagination-btns">
                @if ($products->onFirstPage())
                    <span class="pg-disabled"><i class="bi bi-chevron-left"></i></span>
                @else
                    <a href="{{ $products->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a>
                @endif
                <span class="pg-active">{{ $products->currentPage() }}</span>
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
