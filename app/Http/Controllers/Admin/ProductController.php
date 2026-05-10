<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q'));
        $categoryId = $request->input('category_id');
        $rarity = $request->input('rarity');
        $sort = $request->input('sort', 'newest');

        $query = Product::query()
            ->with(['category', 'primaryImage'])
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . $search . '%';
                $q->where(function ($inner) use ($like) {
                    $inner->where('name', 'ilike', $like)
                        ->orWhere('sku', 'ilike', $like);
                });
            })
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($rarity, fn ($q) => $q->where('rarity', $rarity));

        $query = match ($sort) {
            'name_asc' => $query->orderBy('name'),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'stock_asc' => $query->orderBy('stock'),
            default => $query->latest('updated_at'),
        };

        $products = $query->paginate(15)->withQueryString();

        $totalProducts = Product::count();
        $lowStockCount = Product::whereColumn('stock', '<=', 'low_stock_threshold')->count();
        $limitedCount = Product::where('is_limited_edition', true)->count();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'stats' => [
                'total' => $totalProducts,
                'categories' => Category::count(),
                'outOfStock' => Product::where('stock', 0)->count(),
                'limited' => $limitedCount,
            ],
            'lowStockCount' => $lowStockCount,
            'limitedCount' => $limitedCount,
            'filters' => compact('search', 'categoryId', 'rarity', 'sort'),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product([
                'status' => 'draft',
                'school' => 'none',
                'rarity' => 'common',
                'stock' => 0,
                'low_stock_threshold' => 5,
            ]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = DB::transaction(function () use ($request) {
            $product = Product::create($this->productData($request));
            $this->storeImages($product, $request);
            $this->syncPrimaryImage($product, $request->integer('primary_image_id') ?: null);

            return $product;
        });

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('admin_success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product->load(['images', 'category']),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        DB::transaction(function () use ($request, $product) {
            $product->update($this->productData($request));
            $this->storeImages($product, $request);
            $this->syncPrimaryImage($product, $request->integer('primary_image_id') ?: null);
        });

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('admin_success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        DB::transaction(function () use ($product) {
            $product->images()->get()->each(fn (ProductImage $image) => $this->deleteImage($image));
            $product->forceDelete();
        });

        return redirect()
            ->route('admin.products.index')
            ->with('admin_success', 'Product deleted.');
    }

    public function destroyImage(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $wasMain = $image->is_main;
        $this->deleteImage($image);

        if ($wasMain) {
            $next = $product->images()->oldest('sort_order')->first();
            $next?->update(['is_main' => true]);
        }

        return back()->with('admin_success', 'Image removed.');
    }

    private function productData(ProductRequest $request): array
    {
        $validated = $request->validated();
        $slug = !empty($validated['slug']) ? $validated['slug'] : Str::slug($validated['name']);
        $sku = !empty($validated['sku']) ? $validated['sku'] : strtoupper(Str::random(8));

        return [
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'sku' => $sku,
            'short_description' => $validated['short_description'] ?? null,
            'full_description' => $validated['full_description'] ?? '',
            'price' => $validated['price'],
            'compare_price' => $validated['compare_price'] ?? null,
            'stock' => $validated['stock'],
            'low_stock_threshold' => $validated['low_stock_threshold'] ?? 5,
            'status' => $validated['status'],
            'school' => $validated['school'] ?? 'none',
            'rarity' => $validated['rarity'],
            'is_featured' => $request->boolean('is_featured'),
            'is_limited_edition' => $request->boolean('is_limited_edition'),
            'published_at' => $validated['status'] === 'active' ? now() : null,
        ];
    }

    private function storeImages(Product $product, ProductRequest $request): void
    {
        foreach ($request->file('images', []) as $file) {
            $path = $file->store('products', 'public');

            $product->images()->create([
                'path' => Storage::url($path),
                'alt_text' => $product->name,
                'sort_order' => (int) $product->images()->max('sort_order') + 1,
                'is_main' => $product->images()->doesntExist(),
            ]);
        }
    }

    private function syncPrimaryImage(Product $product, ?int $imageId): void
    {
        if (!$imageId) {
            return;
        }

        $image = $product->images()->whereKey($imageId)->first();
        if (!$image) {
            return;
        }

        $product->images()->update(['is_main' => false]);
        $image->update(['is_main' => true]);
    }

    private function deleteImage(ProductImage $image): void
    {
        if (str_starts_with($image->path, '/storage/')) {
            Storage::disk('public')->delete(Str::after($image->path, '/storage/'));
        }

        $image->delete();
    }
}
