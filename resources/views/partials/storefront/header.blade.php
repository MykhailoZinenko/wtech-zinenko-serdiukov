<header class="site-header">
    <nav class="navbar navbar-expand-lg py-2" aria-label="Main navigation">
        <div class="container">
            <a class="navbar-brand me-4" href="{{ route('home') }}" aria-label="{{ config('app.name') }} Home">
                <div class="logo-icon placeholder-asset" style="width:44px;height:44px;border-radius:50%;font-size:.45rem;">LOGO</div>
                <span class="logo-text ms-2">
                    <span class="brand-name">{{ config('app.name') }}</span>
                    <span class="brand-sub">Novigrad's Finest Trading Post</span>
                </span>
            </a>

            <button class="navbar-toggler border-0 ms-auto me-3" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav site-nav me-auto">
                    @foreach (($navCategories ?? collect()) as $rootCategory)
                        @if ($rootCategory->children->isNotEmpty())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">{{ $rootCategory->name }}</a>
                                <ul class="dropdown-menu">
                                    @foreach ($rootCategory->children as $child)
                                        <li><a class="dropdown-item" href="{{ route('products.category', ['categorySlug' => $child->slug]) }}">{{ $child->name }}</a></li>
                                    @endforeach
                                    <li><hr class="dropdown-divider" /></li>
                                    <li><a class="dropdown-item" href="{{ route('products.category', ['categorySlug' => $rootCategory->slug]) }}">All {{ $rootCategory->name }}</a></li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('products.category', ['categorySlug' => $rootCategory->slug]) }}">{{ $rootCategory->name }}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>

                <form class="header-search me-3" role="search" action="{{ route('products.index') }}" method="get">
                    <label for="header-search-input" class="visually-hidden">Search products</label>
                    <input id="header-search-input" type="search" name="q" placeholder="Search the emporium…" autocomplete="off" value="{{ request('q') }}" />
                    <button type="submit" aria-label="Submit search"><i class="bi bi-search" aria-hidden="true"></i></button>
                </form>

                <div class="header-actions">
                    @auth
                        <a href="{{ route('dashboard') }}" class="action-btn" aria-label="Your account">
                            <i class="bi bi-person" aria-hidden="true"></i> Account
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="action-btn" aria-label="Log in to your account">
                            <i class="bi bi-person" aria-hidden="true"></i> Account
                        </a>
                    @endauth
                    <a href="{{ route('cart.show') }}" class="action-btn cart-btn" aria-label="View cart">
                        <i class="bi bi-bag" aria-hidden="true"></i>
                        <span id="cart-count-badge" class="cart-count" aria-hidden="true" @if (($cartCount ?? 0) === 0) hidden @endif>{{ $cartCount ?? 0 }}</span>
                        Cart
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>
