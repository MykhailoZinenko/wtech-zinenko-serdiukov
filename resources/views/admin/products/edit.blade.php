@extends('layouts.admin')

@section('title', 'Edit Product')

@php
    $rarityBadge = [
        'common' => 'badge--grey', 'new' => 'badge--grey', 'limited' => 'badge--blue',
        'rare' => 'badge--orange', 'legendary' => 'badge--gold',
    ];
@endphp

@section('content')
<div class="breadcrumb-adm">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <a href="{{ route('admin.products.index') }}">Products</a>
    <i class="bi bi-chevron-right"></i>
    <span class="bc-current">{{ $product->name }}</span>
</div>

<div class="page-hdr">
    <div class="page-hdr__left">
        <h1 class="page-hdr__title">Edit Product</h1>
        <p class="page-hdr__sub">{{ $product->name }} &nbsp;·&nbsp; <span class="badge {{ $rarityBadge[$product->rarity] ?? 'badge--grey' }}">{{ ucfirst($product->rarity) }}</span></p>
    </div>
    <div class="page-hdr__actions">
        <a href="{{ route('products.show', $product) }}" target="_blank" class="btn-base btn-outline-gold"><i class="bi bi-box-arrow-up-right"></i> View in Shop</a>
        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button class="btn-base btn-red" data-confirm="Permanently delete {{ $product->name }}? This cannot be undone."><i class="bi bi-trash"></i> Delete</button>
        </form>
    </div>
</div>

@if (session('admin_success'))
    <div class="form-alert form-alert--success">{{ session('admin_success') }}</div>
@endif

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.products.partials.form', ['submitLabel' => 'Save Changes'])
</form>

@foreach ($product->images as $image)
    <form id="delete-image-{{ $image->id }}" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" method="POST">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection
