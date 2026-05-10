<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartOptionsRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartResolver;
use App\Services\OrderOptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CartController extends Controller
{
    public function __construct(
        private readonly CartResolver $resolver,
        private readonly OrderOptionService $options,
    ) {
    }

    public function show(): View
    {
        $items = $this->resolver->items();
        $selected = $this->options->selectedOptions();
        $subtotal = $this->resolver->subtotal();
        $totals = $this->options->totals($subtotal, $selected['delivery'], $selected['payment']);

        return view('cart.show', [
            'items' => $items,
            'deliveryOptions' => OrderOptionService::DELIVERY_OPTIONS,
            'paymentOptions' => OrderOptionService::PAYMENT_OPTIONS,
            'selectedDelivery' => $selected['delivery'],
            'selectedPayment' => $selected['payment'],
            ...$totals,
        ]);
    }

    public function add(AddToCartRequest $request): Response
    {
        $product = Product::findOrFail($request->integer('product_id'));
        abort_if($product->status !== 'active', 404);

        $this->resolver->addProduct($product, $request->quantityValue());

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

    public function updateOptions(UpdateCartOptionsRequest $request): RedirectResponse
    {
        session([
            'cart.delivery' => $request->deliveryMethod(),
            'cart.payment' => $request->paymentMethod(),
        ]);

        return back()->with('cart_success', 'Delivery and payment updated.');
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
        $subtotal = $this->resolver->subtotal();
        return response()->json([
            'message' => $message,
            'count' => $this->resolver->itemCount(),
            'subtotal' => $subtotal,
            'subtotal_formatted' => number_format($subtotal, 0, ',', ' '),
        ]);
    }

    private function authorizeOwnership(CartItem $item): void
    {
        if ($user = auth()->user()) {
            if ($item->user_id !== $user->id) {
                throw new AccessDeniedHttpException('This cart item does not belong to your cart.');
            }
        } else {
            if ($item->session_id !== session()->getId() || $item->user_id !== null) {
                throw new AccessDeniedHttpException('This cart item does not belong to your cart.');
            }
        }
    }
}
