<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:120'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'school' => ['nullable', 'string', 'in:' . implode(',', Product::SCHOOLS)],
            'rarity' => ['nullable', 'string', 'in:' . implode(',', Product::RARITIES)],
            'sort' => ['nullable', 'string', 'in:price_asc,price_desc,name_asc,name_desc,newest'],
            'category' => ['nullable', 'string', 'max:120'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array{min_price: ?string, max_price: ?string, school: ?string, rarity: ?string}
     */
    public function filterValues(): array
    {
        return [
            'min_price' => $this->input('min_price'),
            'max_price' => $this->input('max_price'),
            'school' => $this->input('school'),
            'rarity' => $this->input('rarity'),
        ];
    }

    public function searchTerm(): ?string
    {
        $q = trim((string) $this->input('q', ''));
        return $q === '' ? null : $q;
    }

    public function sortKey(): ?string
    {
        return $this->input('sort');
    }
}
