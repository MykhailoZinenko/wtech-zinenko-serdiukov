<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $product = $this->route('product');
        $productId = $product?->id;

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($productId)],
            'short_description' => ['nullable', 'string', 'max:500'],
            'full_description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0', 'max:2000000000'],
            'compare_price' => ['nullable', 'integer', 'min:0', 'max:2000000000'],
            'stock' => ['required', 'integer', 'min:0', 'max:2000000000'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', Rule::in(['active', 'draft', 'archived'])],
            'school' => ['nullable', 'string', Rule::in(Product::SCHOOLS)],
            'rarity' => ['required', 'string', Rule::in(Product::RARITIES)],
            'is_featured' => ['nullable'],
            'is_limited_edition' => ['nullable'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'max:4096'],
            'primary_image_id' => ['nullable', 'integer', 'exists:product_images,id'],
        ];
    }
}
