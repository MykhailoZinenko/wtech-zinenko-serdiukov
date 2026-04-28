<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\CartResolver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CartResolver::class);
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // The header partial is rendered on every storefront page; share its data
        // (nav categories + cart badge) only when the categories table actually exists,
        // so artisan commands run before migrations don't blow up.
        View::composer(['partials.storefront.header', 'partials.storefront.footer'], function ($view) {
            $navCategories = Schema::hasTable('categories')
                ? Category::with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
                : collect();

            $view->with('navCategories', $navCategories);
        });

        View::composer('partials.storefront.header', function ($view) {
            $count = 0;
            if (Schema::hasTable('carts')) {
                try {
                    $count = app(CartResolver::class)->itemCount();
                } catch (\Throwable $e) {
                    $count = 0;
                }
            }
            $view->with('cartCount', $count);
        });
    }
}
