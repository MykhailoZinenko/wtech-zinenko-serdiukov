<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    public const SCHOOLS = ['wolf', 'griffin', 'cat', 'bear', 'viper', 'manticore', 'ofieri', 'toussaint', 'none'];
    public const RARITIES = ['common', 'new', 'limited', 'rare', 'legendary'];

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'full_description',
        'price',
        'compare_price',
        'stock',
        'low_stock_threshold',
        'weight',
        'status',
        'school',
        'rarity',
        'is_featured',
        'is_limited_edition',
        'avg_rating',
        'review_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'compare_price' => 'integer',
            'weight' => 'decimal:2',
            'avg_rating' => 'decimal:2',
            'review_count' => 'integer',
            'is_featured' => 'boolean',
            'is_limited_edition' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true)->latest();
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /* ---------- Accessors ---------- */

    protected function formattedPrice(): Attribute
    {
        return Attribute::get(fn () => $this->formatAmount($this->price));
    }

    protected function formattedComparePrice(): Attribute
    {
        return Attribute::get(fn () => $this->compare_price ? $this->formatAmount($this->compare_price) : null);
    }

    protected function isOnSale(): Attribute
    {
        return Attribute::get(fn () => $this->compare_price !== null && (int) $this->compare_price > (int) $this->price);
    }

    protected function isNew(): Attribute
    {
        return Attribute::get(fn () => $this->published_at && $this->published_at->gt(now()->subDays(30)));
    }

    protected function inStock(): Attribute
    {
        return Attribute::get(fn () => $this->stock > 0 && $this->status === 'active');
    }

    protected function displayImageUrl(): Attribute
    {
        return Attribute::get(function () {
            $img = $this->relationLoaded('primaryImage') ? $this->primaryImage : $this->primaryImage()->first();
            if ($img) {
                return $img->path;
            }
            $first = $this->relationLoaded('images') ? $this->images->first() : $this->images()->first();
            return $first?->path;
        });
    }

    private function formatAmount(int|float|string|null $value): string
    {
        if ($value === null) {
            return '0';
        }
        return number_format((int) $value, 0, ',', ' ');
    }

    /* ---------- Scopes ---------- */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeInCategory(Builder $query, Category $category): Builder
    {
        return $query->whereIn('category_id', $category->descendantIds());
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
        if (!empty($filters['school']) && in_array($filters['school'], self::SCHOOLS, true)) {
            $query->where('school', $filters['school']);
        }
        if (!empty($filters['rarity']) && in_array($filters['rarity'], self::RARITIES, true)) {
            $query->where('rarity', $filters['rarity']);
        }
        return $query;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }
        $like = '%' . $term . '%';
        return $query->where(function (Builder $q) use ($like) {
            $q->where('name', 'ilike', $like)
              ->orWhere('short_description', 'ilike', $like)
              ->orWhere('full_description', 'ilike', $like)
              ->orWhere('sku', 'ilike', $like);
        });
    }

    public function scopeSort(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'newest' => $query->orderByDesc('published_at'),
            default => $query->orderByDesc('is_featured')->orderByDesc('published_at'),
        };
    }
}
