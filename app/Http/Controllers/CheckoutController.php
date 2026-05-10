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
        $items = $this->cartResolver->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.show')->with('cart_success', 'Your cart is empty.');
        }

        $defaults = $this->options->selectedOptions();
        $subtotal = $this->cartResolver->subtotal();
        $totals = $this->options->totals($subtotal, $defaults['delivery'], $defaults['payment']);

        return view('checkout.show', [
            'items' => $items,
            'deliveryOptions' => OrderOptionService::DELIVERY_OPTIONS,
            'paymentOptions' => OrderOptionService::PAYMENT_OPTIONS,
            'selectedDelivery' => $defaults['delivery'],
            'selectedPayment' => $defaults['payment'],
            ...$totals,
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $items = $this->cartResolver->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.show')->with('cart_success', 'Your cart is empty.');
        }

        foreach ($items as $item) {
            if (! $item->product || $item->quantity > $item->product->stock) {
                $name = $item->product?->name ?? 'a product';
                return redirect()->route('cart.show')->with(
                    'cart_error',
                    "Insufficient stock for {$name}."
                );
            }
        }

        $validated = $request->validated();
        $subtotal = $this->cartResolver->subtotal();
        $shippingMethod = \App\Models\ShippingMethod::findOrFail($validated['shipping_method_id']);
        $shippingCost = $shippingMethod->cost;
        $total = $subtotal + $shippingCost;

        $order = retry(3, fn () => DB::transaction(function () use ($items, $validated, $subtotal, $shippingCost, $total) {
            $order = Order::create([
                'order_number' => $this->nextOrderNumber(),
                'user_id' => Auth::id(),
                'shipping_method_id' => $validated['shipping_method_id'],
                'payment_method_id' => $validated['payment_method_id'],
                'status' => 'pending',
                'ship_first_name' => $validated['first_name'],
                'ship_last_name' => $validated['last_name'],
                'ship_street' => $validated['address'],
                'ship_city' => $validated['city'],
                'ship_postal_code' => $validated['postal_code'],
                'ship_region' => $validated['region'],
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => 0,
                'total' => $total,
                'payment_status' => 'pending',
            ]);

            foreach ($items as $item) {
                $this->createOrderItem($order, $item);
                $item->product->decrement('stock', $item->quantity);
            }

            $this->cartResolver->clearItems();
            session()->forget(['cart.delivery', 'cart.payment']);

            return $order;
        }), when: fn ($e) => str_contains($e->getMessage(), 'orders_order_number_unique'));

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
            'product_sku' => $product?->sku ?? 'N/A',
            'quantity' => $item->quantity,
            'unit_price' => $product?->price ?? 0,
            'line_total' => ($product?->price ?? 0) * $item->quantity,
        ]);
    }

    private function nextOrderNumber(): string
    {
        return 'WW-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
    }

    private function canSeeOrder(Order $order): bool
    {
        return Auth::check()
            && (Auth::id() === $order->user_id || Auth::user()->isAdmin());
    }
}
