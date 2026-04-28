<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'currency',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Add (or increment) a product line on this cart.
     */
    public function addProduct(Product $product, int $quantity = 1): CartItem
    {
        $quantity = max(1, $quantity);

        $item = $this->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->quantity = min(99, $item->quantity + $quantity);
            $item->unit_price = $product->price;
            $item->save();
            return $item;
        }

        return $this->items()->create([
            'product_id' => $product->id,
            'quantity' => min(99, $quantity),
            'unit_price' => $product->price,
        ]);
    }

    public function subtotal(): float
    {
        return (float) $this->items->sum(fn (CartItem $item) => $item->lineTotal);
    }

    public function itemCount(): int
    {
        return (int) $this->items->sum('quantity');
    }

    /**
     * Merge another cart's items into this one and delete the source.
     */
    public function mergeFrom(Cart $other): void
    {
        if ($other->id === $this->id) {
            return;
        }

        foreach ($other->items()->with('product')->get() as $sourceItem) {
            if (!$sourceItem->product) {
                continue;
            }
            $this->addProduct($sourceItem->product, $sourceItem->quantity);
        }

        $other->delete();
    }
}
