<aside class="card-base account-sidebar">
    <div class="account-sidebar__greeting">
        <span class="account-sidebar__name">{{ auth()->user()->full_name }}</span>
        <span class="account-sidebar__email">{{ auth()->user()->email }}</span>
    </div>

    <ul class="account-nav">
        <li>
            <a href="{{ route('account.profile') }}" class="account-nav__link @if(request()->routeIs('account.profile')) active @endif">
                <i class="bi bi-person" aria-hidden="true"></i> Profile
            </a>
        </li>
        <li>
            <a href="{{ route('account.orders') }}" class="account-nav__link @if(request()->routeIs('account.orders')) active @endif">
                <i class="bi bi-receipt" aria-hidden="true"></i> Orders
            </a>
        </li>
        <li>
            <a href="{{ route('account.wishlist') }}" class="account-nav__link @if(request()->routeIs('account.wishlist')) active @endif">
                <i class="bi bi-heart" aria-hidden="true"></i> Wishlist
            </a>
        </li>
    </ul>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn-base btn-outline-gold btn-full btn-sm">
            <i class="bi bi-box-arrow-left" aria-hidden="true"></i> Sign Out
        </button>
    </form>
</aside>
