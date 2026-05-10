@extends('layouts.admin')

@section('title', 'Edit Product')

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
        <p class="page-hdr__sub">{{ $product->sku }} · {{ $product->images->count() }} images</p>
    </div>
    <div class="page-hdr__actions">
        <a href="{{ route('products.show', $product) }}" class="btn-base btn-outline-gold" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View</a>
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
