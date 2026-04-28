<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function lineTotal(): Attribute
    {
        return Attribute::get(fn () => (float) $this->unit_price * (int) $this->quantity);
    }

    protected function formattedLineTotal(): Attribute
    {
        return Attribute::get(fn () => number_format($this->lineTotal, 0, ',', ' '));
    }

    protected function formattedUnitPrice(): Attribute
    {
        return Attribute::get(fn () => number_format((float) $this->unit_price, 0, ',', ' '));
    }
}
