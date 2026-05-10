<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    public $timestamps = false;
    const UPDATED_AT = null;

    protected $fillable = [
        'product_id', 'user_id', 'order_id', 'type',
        'quantity_change', 'stock_after', 'reason', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity_change' => 'integer',
            'stock_after' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
