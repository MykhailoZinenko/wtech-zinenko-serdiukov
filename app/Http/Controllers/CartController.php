<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CartController extends Controller
{
    public function __construct(private readonly CartResolver $resolver)
    {
    }

    public function show(): View
    {
        $cart = $this->resolver->resolve();

        return view('cart.show', [
            'cart' => $cart,
        ]);
    }

    public function add(AddToCartRequest $request): Response
    {
        $product = Product::findOrFail($request->integer('product_id'));
        abort_if($product->status !== 'active', 404);

        $cart = $this->resolver->resolve();
        $cart->addProduct($product, $request->quantityValue());

        $message = "Added {$product->name} to your cart.";

        if ($request->expectsJson() || $request->ajax()) {
            return $this->cartJson($message);
        }

        return back()->with('cart_success', $message);
    }

    public function update(UpdateCartItemRequest $request, CartItem $item): Response
    {
        $this->authorizeOwnership($item);

        $item->update(['quantity' => $request->integer('quantity')]);

        if ($request->expectsJson() || $request->ajax()) {
            return $this->cartJson('Cart updated.');
        }

        return back()->with('cart_success', 'Cart updated.');
    }

    public function remove(CartItem $item): Response
    {
        $this->authorizeOwnership($item);

        $item->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return $this->cartJson('Item removed.');
        }

        return back()->with('cart_success', 'Item removed.');
    }

    private function cartJson(string $message): JsonResponse
    {
        $cart = $this->resolver->resolve();
        return response()->json([
            'message' => $message,
            'count' => $cart->itemCount(),
            'subtotal' => $cart->subtotal(),
            'subtotal_formatted' => number_format($cart->subtotal(), 0, ',', ' '),
        ]);
    }

    private function authorizeOwnership(CartItem $item): void
    {
        $cart = $this->resolver->resolve();
        if ($item->cart_id !== $cart->id) {
            throw new AccessDeniedHttpException('This cart item does not belong to your cart.');
        }
    }
}
