<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartResolver
{
    public function items(): Collection
    {
        if ($user = Auth::user()) {
            return CartItem::where('user_id', $user->id)
                ->with('product.primaryImage')
                ->get();
        }

        return CartItem::where('session_id', Session::getId())
            ->whereNull('user_id')
            ->with('product.primaryImage')
            ->get();
    }

    public function addProduct(\App\Models\Product $product, int $quantity = 1): CartItem
    {
        $quantity = max(1, $quantity);
        $conditions = $this->ownerConditions();

        $item = CartItem::where($conditions)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->quantity = min(99, $item->quantity + $quantity);
            $item->save();
            return $item;
        }

        return CartItem::create([
            ...$conditions,
            'product_id' => $product->id,
            'quantity' => min(99, $quantity),
        ]);
    }

    public function subtotal(): int
    {
        return (int) $this->items()->sum(fn (CartItem $item) => $item->lineTotal);
    }

    public function itemCount(): int
    {
        $conditions = $this->ownerConditions();

        return (int) CartItem::where($conditions)->sum('quantity');
    }

    public function mergeGuestSessionInto(User $user, ?string $guestSessionId): void
    {
        if (!$guestSessionId) {
            return;
        }

        $guestItems = CartItem::where('session_id', $guestSessionId)
            ->whereNull('user_id')
            ->with('product')
            ->get();

        foreach ($guestItems as $guestItem) {
            if (!$guestItem->product) {
                $guestItem->delete();
                continue;
            }

            $existing = CartItem::where('user_id', $user->id)
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($existing) {
                $existing->quantity = min(99, $existing->quantity + $guestItem->quantity);
                $existing->save();
                $guestItem->delete();
            } else {
                $guestItem->update([
                    'user_id' => $user->id,
                    'session_id' => null,
                ]);
            }
        }
    }

    public function clearItems(): void
    {
        CartItem::where($this->ownerConditions())->delete();
    }

    private function ownerConditions(): array
    {
        if ($user = Auth::user()) {
            return ['user_id' => $user->id];
        }

        return ['session_id' => Session::getId(), 'user_id' => null];
    }
}
