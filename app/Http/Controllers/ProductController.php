<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductIndexRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function index(ProductIndexRequest $request, ?string $categorySlug = null): View
    {
        $category = $categorySlug
            ? Category::where('slug', $categorySlug)->where('is_active', true)->firstOrFail()
            : null;

        $query = Product::query()
            ->active()
            ->with(['primaryImage', 'category'])
            ->filter($request->filterValues())
            ->search($request->searchTerm())
            ->sort($request->sortKey());

        if ($category) {
            $query->inCategory($category);
        }

        $products = $query->paginate(12)->withQueryString();

        return view('products.index', [
            'products' => $products,
            'category' => $category,
            'filters' => $request->filterValues(),
            'sort' => $request->sortKey(),
            'searchTerm' => $request->searchTerm(),
        ]);
    }

    public function show(Product $product): View
    {
        abort_if($product->status !== 'active', 404);

        $product->load(['images', 'specifications', 'category', 'reviews.user']);

        $related = Product::query()
            ->active()
            ->with('primaryImage')
            ->inCategory($product->category)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('products.show', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}
