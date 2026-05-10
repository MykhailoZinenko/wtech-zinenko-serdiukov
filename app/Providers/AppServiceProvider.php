<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\CartResolver;
use Illuminate\Pagination\Paginator;
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

        // The header and footer are rendered together, so keep nav data in memory
        // for the current request instead of querying it twice.
        View::composer(['partials.storefront.header', 'partials.storefront.footer'], function ($view) {
            static $navCategories = null;

            if ($navCategories === null) {
                try {
                    $navCategories = Category::with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
                } catch (\Throwable) {
                    $navCategories = collect();
                }
            }

            $view->with('navCategories', $navCategories);
        });

        View::composer('partials.storefront.header', function ($view) {
            $count = 0;
            try {
                $count = app(CartResolver::class)->itemCount();
            } catch (\Throwable) {
                $count = 0;
            }
            $view->with('cartCount', $count);
        });

        View::composer(['components.product-card', 'products.show'], function ($view) {
            static $wishlistedIds = null;

            if ($wishlistedIds === null) {
                if ($user = auth()->user()) {
                    $wishlistedIds = \App\Models\Wishlist::where('user_id', $user->id)
                        ->pluck('product_id')
                        ->all();
                } else {
                    $wishlistedIds = [];
                }
            }

            $view->with('wishlistedIds', $wishlistedIds);
        });
    }
}
