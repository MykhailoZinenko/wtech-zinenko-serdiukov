<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with('children')
            ->orderBy('sort_order')
            ->limit(6)
            ->get()
            ->map(function (Category $category) {
                $categoryIds = $category->descendantIds();
                $products = Product::query()
                    ->active()
                    ->with('primaryImage')
                    ->whereIn('category_id', $categoryIds);

                $category->setAttribute('display_product_count', (clone $products)->count());
                $categoryImage = $category->image_url
                    ?: (clone $products)->whereHas('primaryImage')->first()?->display_image_url
                    ?: "https://picsum.photos/seed/category-{$category->slug}/900/520";

                $category->setAttribute('display_image_url', $categoryImage);

                return $category;
            });

        $featuredProducts = Product::query()
            ->active()
            ->featured()
            ->with(['primaryImage', 'category'])
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        if ($featuredProducts->count() < 4) {
            $featuredProducts = Product::query()
                ->active()
                ->with(['primaryImage', 'category'])
                ->orderByDesc('is_featured')
                ->orderByDesc('published_at')
                ->limit(4)
                ->get();
        }

        $newArrivals = Product::query()
            ->active()
            ->with(['primaryImage', 'category'])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('home', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'newArrivals' => $newArrivals,
        ]);
    }
}
