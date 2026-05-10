@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
<div class="breadcrumb-adm">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <a href="{{ route('admin.products.index') }}">Products</a>
    <i class="bi bi-chevron-right"></i>
    <span class="bc-current">Add Product</span>
</div>

<div class="page-hdr">
    <div class="page-hdr__left">
        <h1 class="page-hdr__title">Add Product</h1>
        <p class="page-hdr__sub">Create a new listing in the Emporium catalogue.</p>
    </div>
    <div class="page-hdr__actions">
        <a href="{{ route('admin.products.index') }}" class="btn-base btn-outline-gold"><i class="bi bi-arrow-left"></i> Cancel</a>
    </div>
</div>

@if ($errors->any())
    <div class="form-alert form-alert--error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.products.partials.form', ['submitLabel' => 'Save Product'])
</form>
@endsection
