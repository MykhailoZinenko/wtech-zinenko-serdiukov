@extends('layouts.storefront')

@section('title', "Novigrad's Finest Trading Post")
@section('main-class', 'home-page')

@section('content')
<section class="hero" aria-labelledby="hero-title">
    <div class="hero-bg">
        <div class="placeholder-asset">HERO BACKGROUND IMAGE</div>
    </div>
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-7 col-xl-6">
                <p class="eyebrow hero-eyebrow">Established in the year of the White Frost</p>
                <h1 id="hero-title">
                    Gear Worthy of<br>
                    <em>a True Witcher</em>
                </h1>
                <p class="hero-desc">
                    From silver blades forged in Kaer Morhen to potions brewed by master herbalists of Skellige — everything a monster hunter, soldier, or city merchant could ever need.
                </p>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="stat-num">1 200+</span>
                        <span class="eyebrow stat-label">Products</span>
                    </div>
                    <div class="hero-stat">
                        <span class="stat-num">{{ $categories->count() }}</span>
                        <span class="eyebrow stat-label">Categories</span>
                    </div>
                    <div class="hero-stat">
                        <span class="stat-num">47</span>
                        <span class="eyebrow stat-label">Regions</span>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="btn-base btn-gold">
                        <i class="bi bi-shop" aria-hidden="true"></i>
                        Browse the Catalogue
                    </a>
                    <a href="{{ route('products.category', ['categorySlug' => 'sets']) }}" class="btn-base btn-outline-gold">Witcher Sets</a>
                </div>
            </div>
        </div>
    </div>
    <a class="hero-scroll" href="#categories" aria-label="Scroll to categories">
        <i class="bi bi-chevron-double-down"></i>
    </a>
</section>

<section class="perks-strip" aria-label="Shop benefits">
    <div class="container">
        <div class="row g-4">
            <div class="col-sm-6 col-lg-3">
                <div class="perk-item">
                    <span class="perk-icon"><i class="bi bi-shield-check" aria-hidden="true"></i></span>
                    <div>
                        <h3 class="perk-title">Authentic Gear</h3>
                        <p class="perk-desc">Every item verified by Guild-certified craftsmen and witchers.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="perk-item">
                    <span class="perk-icon"><i class="bi bi-truck" aria-hidden="true"></i></span>
                    <div>
                        <h3 class="perk-title">Fast Courier</h3>
                        <p class="perk-desc">Delivered via royal post or Nilfgaardian express to all regions.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="perk-item">
                    <span class="perk-icon"><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i></span>
                    <div>
                        <h3 class="perk-title">30-Day Returns</h3>
                        <p class="perk-desc">Unsatisfied? Return any unused item within 30 days, no questions asked.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="perk-item">
                    <span class="perk-icon"><i class="bi bi-lock" aria-hidden="true"></i></span>
                    <div>
                        <h3 class="perk-title">Secure Payment</h3>
                        <p class="perk-desc">Crowns, Orens or Florens — all transactions guarded by mage-level encryption.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="categories" id="categories" aria-labelledby="categories-title">
    <div class="container">
        <header class="section-header">
            <span class="eyebrow section-eyebrow">What are you hunting for?</span>
            <h2 class="section-title" id="categories-title">Shop by Category</h2>
            <p class="section-desc">From the ancient forests of Velen to the markets of Novigrad, our wares span every corner of the Continent.</p>
        </header>
        <div class="row g-4">
            @forelse ($categories as $category)
                <div class="col-sm-6 col-lg-4">
                    <a href="{{ route('products.category', ['categorySlug' => $category->slug]) }}" class="card-base category-card" aria-label="Browse {{ $category->name }}">
                        <div class="category-card__img">
                            @if ($category->display_image_url)
                                <img src="{{ $category->display_image_url }}" alt="{{ $category->name }}" loading="lazy">
                            @else
                                <div class="placeholder-asset">IMAGE: {{ $category->name }}</div>
                            @endif
                        </div>
                        <div class="category-card__body">
                            <h3 class="category-card__name">{{ $category->name }}</h3>
                            <p class="category-card__count">
                                {{ $category->display_product_count }} {{ $category->display_product_count === 1 ? 'item' : 'items' }}
                            </p>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="card-base products-empty">
                        <p>No categories are available yet.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="featured-products" aria-labelledby="featured-title">
    <div class="container">
        <header class="section-header">
            <span class="eyebrow section-eyebrow">Handpicked by our merchants</span>
            <h2 class="section-title" id="featured-title">Featured Products</h2>
            <p class="section-desc">Top picks across all categories, trusted by witchers, soldiers, and scholars throughout the Continent.</p>
        </header>
        @if ($featuredProducts->isNotEmpty())
            <div class="row g-4">
                @foreach ($featuredProducts as $product)
                    <div class="col-sm-6 col-xl-3">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('products.index') }}" class="btn-base btn-outline-gold">View All Products <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
        @else
            <div class="card-base products-empty">
                <p>No featured products are available yet.</p>
            </div>
        @endif
    </div>
</section>

<section class="promo-banner" aria-labelledby="promo-title">
    <div class="promo-banner__bg">
        <div class="placeholder-asset">PROMO BANNER BACKGROUND IMAGE</div>
    </div>
    <div class="container promo-banner__content py-5">
        <div class="row">
            <div class="col-lg-7">
                <span class="promo-tag"><i class="bi bi-lightning-fill" aria-hidden="true"></i> Seasonal Sale: Velen's Feast</span>
                <h2 class="promo-banner__title" id="promo-title">Up to 40% Off<br>Select Alchemy &amp; Armor</h2>
                <p class="promo-banner__desc">Limited time offer from Novigrad's merchant guild. Stock up before the next witcher contract season begins.</p>
                <div class="promo-timer" role="timer" aria-label="Sale countdown">
                    <div class="timer-unit">
                        <span class="t-num" id="timer-days">03</span>
                        <span class="eyebrow t-label">Days</span>
                    </div>
                    <div class="timer-unit">
                        <span class="t-num" id="timer-hours">14</span>
                        <span class="eyebrow t-label">Hours</span>
                    </div>
                    <div class="timer-unit">
                        <span class="t-num" id="timer-mins">27</span>
                        <span class="eyebrow t-label">Mins</span>
                    </div>
                    <div class="timer-unit">
                        <span class="t-num" id="timer-secs">09</span>
                        <span class="eyebrow t-label">Secs</span>
                    </div>
                </div>
                <a href="{{ route('products.index', ['max_price' => 1000]) }}" class="btn-base btn-red">
                    <i class="bi bi-tag-fill" aria-hidden="true"></i>
                    Shop the Sale
                </a>
            </div>
        </div>
    </div>
</section>

<section class="new-arrivals" aria-labelledby="new-arrivals-title">
    <div class="container">
        <header class="section-header">
            <span class="eyebrow section-eyebrow">Just landed in Novigrad</span>
            <h2 class="section-title" id="new-arrivals-title">New Arrivals</h2>
        </header>
        @if ($newArrivals->isNotEmpty())
            <div class="row g-4">
                @foreach ($newArrivals as $product)
                    <div class="col-sm-6 col-lg-4">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="btn-base btn-outline-gold">View All New Arrivals <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
        @else
            <div class="card-base products-empty">
                <p>No new arrivals are available yet.</p>
            </div>
        @endif
    </div>
</section>

<section class="flavor-section" aria-label="Merchant's words">
    <div class="container">
        <div class="flavor-quote">
            <blockquote>
                Not all monsters carry blades. Some carry coin. Know your enemy — whether it stalks the forests of Velen or the counting houses of Novigrad. The well-equipped witcher survives either.
            </blockquote>
            <cite>— Vesemir of Kaer Morhen, Master Witcher</cite>
        </div>
    </div>
</section>

<section class="newsletter" aria-labelledby="newsletter-title">
    <div class="container">
        <div class="newsletter-box">
            <h2 id="newsletter-title">Join the Witcher's Guild</h2>
            <p>Subscribe to receive contract alerts, new stock notifications, and exclusive discounts for active witchers.</p>
            @if (session('newsletter_success'))
                <div class="cart-flash">{{ session('newsletter_success') }}</div>
            @endif
            <form class="newsletter-form" action="{{ route('newsletter.store') }}" method="POST">
                @csrf
                <label for="newsletter-email" class="visually-hidden">Your email address</label>
                <input id="newsletter-email" type="email" name="email" placeholder="Enter your email address..." autocomplete="email" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>
</section>
@endsection
