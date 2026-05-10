<?php

namespace App\Services;

class OrderOptionService
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

    public const FREE_COURIER_THRESHOLD = 500;

    public function selectedOptions(): array
    {
        $delivery = session('cart.delivery', 'courier');
        $payment = session('cart.payment', 'card');

        return [
            'delivery' => array_key_exists($delivery, self::DELIVERY_OPTIONS) ? $delivery : 'courier',
            'payment' => array_key_exists($payment, self::PAYMENT_OPTIONS) ? $payment : 'card',
        ];
    }

    public function deliveryFee(string $method, float $subtotal): int
    {
        if ($method === 'courier' && $subtotal >= self::FREE_COURIER_THRESHOLD) {
            return 0;
        }

        return self::DELIVERY_OPTIONS[$method]['fee'];
    }

    public function paymentFee(string $method): int
    {
        return self::PAYMENT_OPTIONS[$method]['fee'];
    }

    public function totals(float $subtotal, string $delivery, string $payment): array
    {
        $deliveryFee = $this->deliveryFee($delivery, $subtotal);
        $paymentFee = $this->paymentFee($payment);

        return [
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'paymentFee' => $paymentFee,
            'total' => $subtotal + $deliveryFee + $paymentFee,
        ];
    }
}
