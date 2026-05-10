<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q'));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $like = '%' . $q . '%';

        $products = Product::where('name', 'ilike', $like)
            ->orWhere('sku', 'ilike', $like)
            ->with('primaryImage')
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'type' => 'product',
                'label' => $p->name,
                'sub' => $p->sku,
                'url' => route('admin.products.edit', $p),
                'image' => $p->display_image_url,
            ]);

        $orders = Order::where('order_number', 'ilike', $like)
            ->orWhere('ship_first_name', 'ilike', $like)
            ->orWhere('ship_last_name', 'ilike', $like)
            ->orWhere('customer_email', 'ilike', $like)
            ->limit(5)
            ->get()
            ->map(fn ($o) => [
                'type' => 'order',
                'label' => $o->order_number,
                'sub' => $o->ship_first_name . ' ' . $o->ship_last_name,
                'url' => route('admin.orders.show', $o),
                'image' => null,
            ]);

        return response()->json($products->concat($orders)->values());
    }
}
