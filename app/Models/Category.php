<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'image_path',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Return this category's id together with all descendant ids.
     *
     * @return array<int>
     */
    public function descendantIds(): array
    {
        $ids = [$this->id];

        foreach ($this->children()->with('children')->get() as $child) {
            $ids = array_merge($ids, $child->descendantIds());
        }

        return $ids;
    }

    /**
     * Walk up the parent chain and return ancestors from root to self.
     */
    protected function breadcrumb(): Attribute
    {
        return Attribute::get(function (): Collection {
            $chain = collect([$this]);
            $node = $this;
            while ($node->parent) {
                $chain->prepend($node->parent);
                $node = $node->parent;
            }
            return $chain;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
