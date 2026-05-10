<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingPaymentSeeder extends Seeder
{
    public function run(): void
    {
        ShippingMethod::insert([
            [
                'name' => 'Courier delivery',
                'description' => 'Delivered to your address in 2-4 working days.',
                'cost' => 50,
                'estimated_days' => '2-4',
                'sort_order' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Store pickup',
                'description' => 'Collect your order at Hierarch Square.',
                'cost' => 0,
                'estimated_days' => '1',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Express courier',
                'description' => 'Priority delivery on the next working day.',
                'cost' => 120,
                'estimated_days' => '1',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        PaymentMethod::insert([
            [
                'name' => 'Card payment',
                'description' => 'Pay securely by card when submitting the order.',
                'sort_order' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bank transfer',
                'description' => 'Receive payment instructions after checkout.',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cash on delivery',
                'description' => 'Pay the courier when your package arrives.',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
