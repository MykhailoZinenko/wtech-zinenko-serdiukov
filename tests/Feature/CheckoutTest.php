<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_before_checkout(): void
    {
        $response = $this
            ->withHeader('Accept', 'text/html')
            ->get(route('checkout.show'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_submit_checkout(): void
    {
        $response = $this
            ->withHeader('Accept', 'text/html')
            ->post(route('checkout.store'), [
                'first_name' => 'Geralt',
                'last_name' => 'of Rivia',
                'email' => 'geralt@example.com',
                'address' => 'Hierarch Square 1',
                'city' => 'Novigrad',
                'postal_code' => '11000',
                'region' => 'novigrad',
                'delivery' => 'courier',
                'payment' => 'card',
            ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_customer_can_create_order_from_cart(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Swords',
            'slug' => 'swords',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Silver Sword',
            'slug' => 'silver-sword',
            'sku' => 'SW-001',
            'price' => 200,
            'stock_quantity' => 10,
            'status' => 'active',
            'school' => 'wolf',
            'rarity' => 'rare',
            'published_at' => now(),
        ]);

        $cart = Cart::create([
            'user_id' => $user->id,
            'currency' => 'Crowns',
        ]);
        $cart->addProduct($product, 2);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'first_name' => 'Geralt',
            'last_name' => 'of Rivia',
            'email' => 'geralt@example.com',
            'phone' => '+421900123456',
            'address' => 'Hierarch Square 1',
            'city' => 'Novigrad',
            'postal_code' => '11000',
            'region' => 'novigrad',
            'notes' => 'No portals.',
            'delivery' => 'courier',
            'payment' => 'cash_on_delivery',
        ]);

        $order = Order::firstOrFail();

        $response->assertRedirect(route('checkout.success', $order));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'email' => 'geralt@example.com',
            'delivery_method' => 'courier',
            'payment_method' => 'cash_on_delivery',
            'subtotal' => 400,
            'delivery_fee' => 50,
            'payment_fee' => 25,
            'total' => 475,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Silver Sword',
            'quantity' => 2,
            'unit_price' => 200,
            'line_total' => 400,
        ]);
        $this->assertSame(0, $cart->items()->count());
    }
}
