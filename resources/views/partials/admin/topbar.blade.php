<header class="admin-topbar">
    <button class="topbar-toggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>
    <div class="topbar-search" data-search-url="{{ route('admin.search') }}">
        <i class="bi bi-search"></i>
        <input type="search" id="admin-search" placeholder="Search products, orders…" autocomplete="off" />
        <div class="search-popover" id="search-popover" hidden></div>
    </div>
    <div class="topbar-actions">
        <a href="{{ route('admin.orders.index') }}" class="topbar-btn" title="Orders">
            <i class="bi bi-receipt"></i>
        </a>
        <div class="topbar-avatar" title="{{ auth()->user()->full_name }}">{{ auth()->user()->initials }}</div>
    </div>
</header>
