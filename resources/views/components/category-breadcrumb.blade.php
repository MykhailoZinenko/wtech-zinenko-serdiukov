@props([
    'category' => null,
    'product' => null,
])
@php
    $crumbs = [];
    if ($category) {
        foreach ($category->breadcrumb as $node) {
            $crumbs[] = ['label' => $node->name, 'url' => route('products.category', ['categorySlug' => $node->slug])];
        }
    } elseif ($product && $product->category) {
        foreach ($product->category->breadcrumb as $node) {
            $crumbs[] = ['label' => $node->name, 'url' => route('products.category', ['categorySlug' => $node->slug])];
        }
        $crumbs[] = ['label' => $product->name, 'url' => null];
    }
@endphp
<nav class="products-breadcrumb mb-4" aria-label="Breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Shop</a></li>
        @foreach ($crumbs as $i => $crumb)
            @if ($crumb['url'] && $i < count($crumbs) - 1)
                <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
            @else
                <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>
