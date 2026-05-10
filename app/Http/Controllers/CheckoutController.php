<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\CartItem;
use App\Models\Order;
use App\Services\CartResolver;
use App\Services\OrderOptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartResolver $cartResolver,
        private readonly OrderOptionService $options,
    ) {
    }

    public function show(): View|RedirectResponse
    {
        $cart = $this->cartResolver->resolve();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.show')->with('cart_success', 'Your cart is empty.');
        }

        $defaults = $this->options->selectedOptions();
        $totals = $this->options->totals($cart->subtotal(), $defaults['delivery'], $defaults['payment']);

        return view('checkout.show', [
            'cart' => $cart,
            'deliveryOptions' => OrderOptionService::DELIVERY_OPTIONS,
            'paymentOptions' => OrderOptionService::PAYMENT_OPTIONS,
            'selectedDelivery' => $defaults['delivery'],
            'selectedPayment' => $defaults['payment'],
            ...$totals,
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $cart = $this->cartResolver->resolve();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.show')->with('cart_success', 'Your cart is empty.');
        }

        foreach ($cart->items as $item) {
            if (! $item->product || $item->quantity > $item->product->stock_quantity) {
                $name = $item->product?->name ?? 'a product';
                return redirect()->route('cart.show')->with(
                    'cart_error',
                    "Insufficient stock for {$name}."
                );
            }
        }

        $validated = $request->validated();
        $subtotal = $cart->subtotal();
        $totals = $this->options->totals($subtotal, $validated['delivery'], $validated['payment']);

        $order = DB::transaction(function () use ($cart, $validated, $subtotal, $totals) {
            $order = Order::create([
                'number' => $this->nextOrderNumber(),
                'user_id' => Auth::id(),
                'status' => 'new',
                'currency' => $cart->currency,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'region' => $validated['region'],
                'notes' => $validated['notes'] ?? null,
                'delivery_method' => $validated['delivery'],
                'payment_method' => $validated['payment'],
                'subtotal' => $subtotal,
                'delivery_fee' => $totals['deliveryFee'],
                'payment_fee' => $totals['paymentFee'],
                'total' => $totals['total'],
            ]);

            foreach ($cart->items as $item) {
                $this->createOrderItem($order, $item);
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            $cart->items()->delete();
            session()->forget(['cart.delivery', 'cart.payment']);

            return $order;
        });

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order): View
    {
        abort_unless($this->canSeeOrder($order), 404);

        return view('checkout.success', [
            'order' => $order->load('items'),
            'deliveryOptions' => OrderOptionService::DELIVERY_OPTIONS,
            'paymentOptions' => OrderOptionService::PAYMENT_OPTIONS,
        ]);
    }

    private function createOrderItem(Order $order, CartItem $item): void
    {
        $product = $item->product;

        $order->items()->create([
            'product_id' => $product?->id,
            'product_name' => $product?->name ?? 'Unavailable product',
            'product_slug' => $product?->slug,
            'sku' => $product?->sku,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'line_total' => $item->line_total,
        ]);
    }

    private function nextOrderNumber(): string
    {
        do {
            $number = 'WWE-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Order::where('number', $number)->exists());

        return $number;
    }

    private function canSeeOrder(Order $order): bool
    {
        if ($order->user_id) {
            return Auth::id() === $order->user_id || Auth::user()?->isAdmin();
        }

        return true;
    }
}
