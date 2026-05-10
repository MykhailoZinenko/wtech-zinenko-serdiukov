@props(['product'])
@php
    $image = $product->display_image_url;
    $badgeText = null;
    $badgeClass = null;
    if ($product->rarity === 'legendary') {
        $badgeText = 'Legendary';
        $badgeClass = 'product-card__badge--rare';
    } elseif ($product->rarity === 'rare') {
        $badgeText = 'Rare';
        $badgeClass = 'product-card__badge--rare';
    } elseif ($product->is_new) {
        $badgeText = 'New';
        $badgeClass = 'product-card__badge--new';
    } elseif ($product->is_on_sale) {
        $badgeText = 'Sale';
        $badgeClass = 'product-card__badge--limited';
    }
@endphp
<article class="card-base product-card">
    <a href="{{ route('products.show', $product) }}" class="product-card__img-wrap">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $product->name }}" loading="lazy" />
        @else
            <div class="placeholder-asset">IMAGE: {{ $product->name }}</div>
        @endif
        @if ($badgeText)
            <span class="product-card__badge {{ $badgeClass }}">{{ $badgeText }}</span>
        @endif
        @auth
            @php $isWishlisted = in_array($product->id, $wishlistedIds ?? []); @endphp
            <button type="button"
                class="wishlist-btn product-card__wishlist"
                data-wishlist-toggle="{{ $product->id }}"
                aria-label="{{ $isWishlisted ? 'Remove' : 'Add' }} {{ $product->name }} {{ $isWishlisted ? 'from' : 'to' }} wishlist"
                @if($isWishlisted) style="color:var(--clr-red-light);border-color:var(--clr-red);" @endif>
                <i class="bi {{ $isWishlisted ? 'bi-heart-fill' : 'bi-heart' }}"></i>
            </button>
        @endauth
    </a>
    <div class="product-card__body">
        @if ($product->category)
            <p class="eyebrow product-card__category">{{ $product->category->name }}</p>
        @endif
        <x-product-rating :rating="$product->avg_rating" :count="$product->review_count" />
        <h3 class="product-card__name"><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
        <p class="product-card__desc">{{ $product->short_description }}</p>
    </div>
    <div class="product-card__footer">
        <div class="product-price">
            <span class="product-price__current">{{ $product->formatted_price }} <span class="eyebrow product-price__unit">Crowns</span></span>
            @if ($product->is_on_sale)
                <span class="product-price__old">{{ $product->formatted_compare_price }} Crowns</span>
            @endif
        </div>
        <form action="{{ route('cart.add') }}" method="POST" class="d-inline" data-cart-add>
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}" />
            <input type="hidden" name="quantity" value="1" />
            <button type="submit" class="btn-add-cart" aria-label="Add {{ $product->name }} to cart">
                <i class="bi bi-bag-plus"></i>
            </button>
        </form>
    </div>
</article>
