@extends('layouts.storefront')

@section('title', $product->name)
@section('main-class', 'product-page')

@php
    $images = $product->images;
    $primary = $product->primaryImage ?? $images->first();
    $stockLabel = $product->in_stock ? 'Yes' : 'Out of stock';
    $rarityLabel = ucfirst($product->rarity);
    $schoolLabel = $product->school === 'generic' ? 'Generic' : 'School of the ' . ucfirst($product->school);

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

@section('content')
<div class="container py-4 py-lg-5">

    <x-category-breadcrumb :product="$product" />

    @if (session('cart_success'))
        <div class="cart-flash">{{ session('cart_success') }}</div>
    @endif

    <div class="row g-4 g-lg-5">
        <div class="col-lg-6">
            <div class="sticky-sidebar product-gallery">
                <div class="product-gallery__main">
                    @if ($primary && $primary->path)
                        <img src="{{ $primary->path }}" alt="{{ $primary->alt_text ?? $product->name }}" class="product-gallery__img" />
                    @else
                        <div class="placeholder-asset product-gallery__img">IMAGE: {{ $product->name }}</div>
                    @endif
                    @if ($badgeText)
                        <span class="product-gallery__badge product-card__badge {{ $badgeClass }}">{{ $badgeText }}</span>
                    @endif
                </div>
                @if ($images->count() > 1)
                    <div class="product-gallery__thumbs">
                        @foreach ($images as $i => $image)
                            <button type="button" class="product-gallery__thumb {{ $i === 0 ? 'active' : '' }}" aria-label="View image {{ $i + 1 }}">
                                @if ($image->path)
                                    <img src="{{ $image->path }}" alt="" />
                                @else
                                    <div class="placeholder-asset">{{ $i + 1 }}</div>
                                @endif
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="product-detail">
                @if ($product->category)
                    <p class="product-detail__category">{{ $product->category->name }}</p>
                @endif
                <h1 class="product-detail__name">{{ $product->name }}</h1>
                <x-product-rating :rating="$product->avg_rating" :count="$product->review_count . ' reviews'" class="product-rating product-detail__rating" />

                <div class="product-detail__price">
                    <span class="product-price__current">{{ $product->formatted_price }} <span class="eyebrow product-price__unit">Crowns</span></span>
                    @if ($product->is_on_sale)
                        <span class="product-price__old">{{ $product->formatted_compare_price }} Crowns</span>
                    @endif
                </div>

                <p class="product-detail__desc">{{ $product->full_description ?? $product->short_description }}</p>

                <div class="product-detail__actions">
                    <form action="{{ route('cart.add') }}" method="POST" class="product-detail__add-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}" />
                        <div class="product-detail__qty">
                            <label for="qty-input" class="visually-hidden">Quantity</label>
                            <input type="number" id="qty-input" name="quantity" min="1" max="99" value="1" class="input-base product-detail__qty-input" />
                        </div>
                        <button type="submit" class="btn-base btn-gold btn-flex" aria-label="Add {{ $product->name }} to cart" {{ $product->in_stock ? '' : 'disabled' }}>
                            <i class="bi bi-bag-plus"></i> Add to Cart
                        </button>
                        <button type="button" class="wishlist-btn product-detail__wishlist" aria-label="Add to wishlist">
                            <i class="bi bi-heart"></i>
                        </button>
                    </form>

                    @if ($cartItem)
                        <form action="{{ route('cart.remove', $cartItem) }}" method="POST" class="product-detail__remove-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-base btn-red product-detail__remove" aria-label="Remove {{ $product->name }} from cart">
                                <i class="bi bi-trash"></i> Remove from Cart
                            </button>
                        </form>
                    @endif
                </div>

                <div class="product-detail__meta">
                    <p><span class="product-detail__meta-label">SKU</span> {{ $product->sku }}</p>
                    <p><span class="product-detail__meta-label">School</span> {{ $schoolLabel }}</p>
                    <p><span class="product-detail__meta-label">Rarity</span> {{ $rarityLabel }}</p>
                    <p><span class="product-detail__meta-label">In stock</span> {{ $stockLabel }}</p>
                </div>
            </div>
        </div>
    </div>

    @if ($product->specifications->isNotEmpty())
        <section class="product-specs mt-5 pt-5" aria-labelledby="specs-title">
            <h2 id="specs-title" class="card-title">Specifications</h2>
            <div class="product-specs__grid">
                @foreach ($product->specifications as $spec)
                    <div class="product-specs__item">
                        <span class="product-specs__label">{{ $spec->key }}</span>
                        <span class="product-specs__value">{{ $spec->value }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if ($related->isNotEmpty())
        <section class="product-related mt-5 pt-5" aria-labelledby="related-title">
            <h2 id="related-title" class="product-related__title">You May Also Like</h2>
            <div class="row g-4 products-grid">
                @foreach ($related as $relatedProduct)
                    <div class="col-12 col-sm-6 col-lg-3">
                        <x-product-card :product="$relatedProduct" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="product-reviews mt-5 pt-5" aria-labelledby="reviews-title">
        <div class="product-reviews__header">
            <h2 id="reviews-title" class="product-reviews__title">Customer Reviews</h2>
            @if ($product->review_count > 0)
                <div class="product-reviews__summary">
                    <x-product-rating :rating="$product->avg_rating" :show-count="false" class="product-rating product-reviews__rating" />
                    <span class="product-reviews__count">{{ $product->review_count }} {{ $product->review_count === 1 ? 'review' : 'reviews' }}</span>
                </div>
            @endif
        </div>

        @if (session('review_success'))
            <div class="cart-flash">{{ session('review_success') }}</div>
        @endif

        <div class="card-base review-submit">
            <h3 class="card-title">Submit a Review</h3>
            <form action="{{ route('reviews.store', $product) }}" method="POST" class="review-submit__form">
                @csrf
                @auth <input type="hidden" name="_auth" value="1" /> @endauth
                <div class="review-submit__row review-submit__row--first">
                    @guest
                        <div class="review-submit__field">
                            <label for="review-name" class="eyebrow review-submit__label">Name</label>
                            <input type="text" id="review-name" name="author_name" class="input-base review-submit__input" placeholder="Your name" value="{{ old('author_name') }}" required />
                            @error('author_name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    @endguest
                    <div class="review-submit__field">
                        <span class="eyebrow review-submit__label">Rating</span>
                        <div class="review-submit__stars" role="group" aria-label="Rate 1 to 5 stars">
                            @for ($s = 1; $s <= 5; $s++)
                                <button type="button" class="review-submit__star {{ old('rating', 0) >= $s ? 'active' : '' }}" data-rating="{{ $s }}" aria-label="{{ $s }} star{{ $s > 1 ? 's' : '' }}">
                                    <i class="bi {{ old('rating', 0) >= $s ? 'bi-star-fill' : 'bi-star' }}"></i>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="review-rating-value" value="{{ old('rating', '') }}" />
                        @error('rating') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="review-submit__row">
                    <label for="review-text" class="eyebrow review-submit__label">Your review</label>
                    <textarea id="review-text" name="body" class="input-base review-submit__textarea" placeholder="Share your experience…" required>{{ old('body') }}</textarea>
                    @error('body') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="review-submit__btn">Submit Review</button>
            </form>
        </div>

        @if ($product->reviews->isNotEmpty())
            <div class="product-reviews__list">
                @foreach ($product->reviews as $review)
                    <article class="card-base review-card">
                        <div class="review-card__meta">
                            <span class="review-card__author">{{ $review->author_name ?? $review->user?->full_name ?? 'Anonymous' }}</span>
                            <x-product-rating :rating="$review->rating" :show-count="false" class="product-rating review-card__rating" />
                            <time class="review-card__date" datetime="{{ $review->created_at->toIso8601String() }}">{{ $review->created_at->format('j M Y') }}</time>
                        </div>
                        <p class="review-card__text">{{ $review->body }}</p>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
