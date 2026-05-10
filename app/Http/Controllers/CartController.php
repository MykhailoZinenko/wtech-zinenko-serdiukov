<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartOptionsRequest;
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
    public const DELIVERY_OPTIONS = [
        'courier' => [
            'label' => 'Courier delivery',
            'description' => 'Delivered to your address in 2-4 working days.',
            'fee' => 50,
        ],
        'pickup' => [
            'label' => 'Store pickup',
            'description' => 'Collect your order at Hierarch Square.',
            'fee' => 0,
        ],
        'express' => [
            'label' => 'Express courier',
            'description' => 'Priority delivery on the next working day.',
            'fee' => 120,
        ],
    ];

    public const PAYMENT_OPTIONS = [
        'card' => [
            'label' => 'Card payment',
            'description' => 'Pay securely by card when submitting the order.',
            'fee' => 0,
        ],
        'bank_transfer' => [
            'label' => 'Bank transfer',
            'description' => 'Receive payment instructions after checkout.',
            'fee' => 0,
        ],
        'cash_on_delivery' => [
            'label' => 'Cash on delivery',
            'description' => 'Pay the courier when your package arrives.',
            'fee' => 25,
        ],
    ];

    public function __construct(private readonly CartResolver $resolver)
    {
    }

    public function show(): View
    {
        $cart = $this->resolver->resolve();
        $options = $this->cartOptions();
        $subtotal = $cart->subtotal();
        $deliveryFee = $this->deliveryFee($options['delivery'], $subtotal);
        $paymentFee = self::PAYMENT_OPTIONS[$options['payment']]['fee'];

        return view('cart.show', [
            'cart' => $cart,
            'deliveryOptions' => self::DELIVERY_OPTIONS,
            'paymentOptions' => self::PAYMENT_OPTIONS,
            'selectedDelivery' => $options['delivery'],
            'selectedPayment' => $options['payment'],
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'paymentFee' => $paymentFee,
            'total' => $subtotal + $deliveryFee + $paymentFee,
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

    private function cartOptions(): array
    {
        $delivery = session('cart.delivery', 'courier');
        $payment = session('cart.payment', 'card');

        return [
            'delivery' => array_key_exists($delivery, self::DELIVERY_OPTIONS) ? $delivery : 'courier',
            'payment' => array_key_exists($payment, self::PAYMENT_OPTIONS) ? $payment : 'card',
        ];
    }

    private function deliveryFee(string $delivery, float $subtotal): int
    {
        if ($delivery === 'courier' && $subtotal >= 500) {
            return 0;
        }

        return self::DELIVERY_OPTIONS[$delivery]['fee'];
    }
}
