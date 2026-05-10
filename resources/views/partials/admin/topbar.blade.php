<header class="admin-topbar">
    <button class="topbar-toggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>
    <form action="{{ route('admin.products.index') }}" method="GET" class="topbar-search">
        <i class="bi bi-search"></i>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products, orders..." />
    </form>
    <div class="topbar-actions">
        <button class="topbar-btn" title="Notifications">
            <i class="bi bi-bell"></i>
        </button>
        <div class="topbar-avatar" title="{{ auth()->user()->full_name }}">{{ auth()->user()->initials }}</div>
    </div>
</header>
