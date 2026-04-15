<header class="site-header">
    <nav class="navbar navbar-expand-lg py-2" aria-label="Main navigation">
        <div class="container">
            <a class="navbar-brand me-4" href="{{ url('/') }}" aria-label="{{ config('app.name') }} — Home">
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Weapons</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('products?category=swords') }}">Swords &amp; Daggers</a></li>
                            <li><a class="dropdown-item" href="{{ url('products?category=axes') }}">Axes &amp; Hammers</a></li>
                            <li><a class="dropdown-item" href="{{ url('products?category=crossbows') }}">Crossbows</a></li>
                            <li><hr class="dropdown-divider" /></li>
                            <li><a class="dropdown-item" href="{{ url('products?category=weapons') }}">All Weapons</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Armor</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('products?category=light-armor') }}">Light Armor</a></li>
                            <li><a class="dropdown-item" href="{{ url('products?category=medium-armor') }}">Medium Armor</a></li>
                            <li><a class="dropdown-item" href="{{ url('products?category=heavy-armor') }}">Heavy Armor</a></li>
                            <li><hr class="dropdown-divider" /></li>
                            <li><a class="dropdown-item" href="{{ url('products?category=sets') }}">Witcher Sets</a></li>
                            <li><a class="dropdown-item" href="{{ url('products?category=armor') }}">All Armor</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Alchemy</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('products?category=potions') }}">Potions &amp; Decoctions</a></li>
                            <li><a class="dropdown-item" href="{{ url('products?category=oils') }}">Blade Oils</a></li>
                            <li><a class="dropdown-item" href="{{ url('products?category=bombs') }}">Bombs</a></li>
                            <li><a class="dropdown-item" href="{{ url('products?category=herbs') }}">Herbs &amp; Ingredients</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('products?category=monster-parts') }}">Monster Parts</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('products?category=gwent') }}">Gwent Cards</a></li>
                </ul>

                <form class="header-search me-3" role="search" action="{{ url('products') }}" method="get">
                    <label for="header-search-input" class="visually-hidden">Search products</label>
                    <input id="header-search-input" type="search" name="q" placeholder="Search the emporium…" autocomplete="off" />
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
                    <a href="{{ url('cart') }}" class="action-btn cart-btn" aria-label="View cart">
                        <i class="bi bi-bag" aria-hidden="true"></i>
                        Cart
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>
