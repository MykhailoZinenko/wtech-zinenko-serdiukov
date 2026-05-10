<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;

class WishlistController extends Controller
{
    public function toggle(Product $product): JsonResponse
    {
        $userId = auth()->id();

        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['wishlisted' => false]);
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $product->id,
            'created_at' => now(),
        ]);

        return response()->json(['wishlisted' => true]);
    }
}
