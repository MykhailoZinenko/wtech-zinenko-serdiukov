<aside class="sidebar">
    <div class="sidebar__brand">
        <span class="sidebar__brand-name">{{ config('app.name') }}</span>
        <span class="sidebar__brand-sub">Admin Panel</span>
    </div>
    <nav class="sidebar__nav">
        <span class="sidebar__section">Overview</span>
        <a href="{{ route('admin.dashboard') }}" class="sidebar__link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <span class="sidebar__section">Catalogue</span>
        <a href="#" class="sidebar__link">
            <i class="bi bi-box-seam"></i> Products
        </a>
        <a href="#" class="sidebar__link">
            <i class="bi bi-plus-circle"></i> Add Product
        </a>
        <span class="sidebar__section">Sales</span>
        <a href="#" class="sidebar__link">
            <i class="bi bi-receipt"></i> Orders
        </a>
    </nav>
    <div class="sidebar__footer">
        <div class="sidebar__user">
            <div class="sidebar__avatar">{{ auth()->user()->initials }}</div>
            <div>
                <span class="sidebar__user-name">{{ auth()->user()->full_name }}</span>
                <span class="sidebar__user-role">{{ ucfirst(auth()->user()->role) }}</span>
            </div>
        </div>
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar__link" style="width:100%;border:none;background:none;text-align:left;cursor:pointer;">
                <i class="bi bi-box-arrow-left"></i> Sign Out
            </button>
        </form>
    </div>
</aside>
