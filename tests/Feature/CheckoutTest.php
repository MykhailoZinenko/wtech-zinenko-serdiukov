<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_checkout(): void
    {
        $response = $this->get(route('checkout.show'));

        $response->assertUnauthorized();
    }

    public function test_guest_cannot_submit_checkout(): void
    {
        $shipping = ShippingMethod::create(['name' => 'Courier', 'cost' => 50, 'estimated_days' => '2-4', 'is_active' => true]);
        $payment = PaymentMethod::create(['name' => 'Cash on Delivery', 'is_active' => true]);

        $response = $this->post(route('checkout.store'), [
            'first_name' => 'Geralt',
            'last_name' => 'of Rivia',
            'email' => 'geralt@example.com',
            'address' => 'Hierarch Square 1',
            'city' => 'Novigrad',
            'postal_code' => '11000',
            'region' => 'novigrad',
            'shipping_method_id' => $shipping->id,
            'payment_method_id' => $payment->id,
        ]);

        $response->assertUnauthorized();
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
            'stock' => 10,
            'status' => 'active',
            'school' => 'wolf',
            'rarity' => 'rare',
            'published_at' => now(),
        ]);

        $shipping = ShippingMethod::create(['name' => 'Courier', 'cost' => 50, 'estimated_days' => '2-4', 'is_active' => true]);
        $payment = PaymentMethod::create(['name' => 'Cash on Delivery', 'is_active' => true]);

        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 2]);

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
            'shipping_method_id' => $shipping->id,
            'payment_method_id' => $payment->id,
        ]);

        $order = Order::firstOrFail();

        $response->assertRedirect(route('checkout.success', $order));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'customer_email' => 'geralt@example.com',
            'shipping_method_id' => $shipping->id,
            'payment_method_id' => $payment->id,
            'subtotal' => 400,
            'shipping_cost' => 50,
            'total' => 450,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Silver Sword',
            'quantity' => 2,
            'unit_price' => 200,
            'line_total' => 400,
        ]);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_checkout_decrements_product_stock(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Potions', 'slug' => 'potions']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Swallow Potion',
            'slug' => 'swallow-potion',
            'sku' => 'POT-001',
            'price' => 50,
            'stock' => 10,
            'status' => 'active',
            'school' => 'none',
            'rarity' => 'common',
            'published_at' => now(),
        ]);

        $shipping = ShippingMethod::create(['name' => 'Pickup', 'cost' => 0, 'estimated_days' => '1-2', 'is_active' => true]);
        $payment = PaymentMethod::create(['name' => 'Card', 'is_active' => true]);

        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 3]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'first_name' => 'Yennefer',
            'last_name' => 'of Vengerberg',
            'email' => 'yen@example.com',
            'address' => 'Aretuza',
            'city' => 'Gors Velen',
            'postal_code' => '20000',
            'region' => 'temeria',
            'shipping_method_id' => $shipping->id,
            'payment_method_id' => $payment->id,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 7,
        ]);
    }

    public function test_checkout_fails_with_insufficient_stock(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Bombs', 'slug' => 'bombs']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Grapeshot',
            'slug' => 'grapeshot',
            'sku' => 'BOM-001',
            'price' => 30,
            'stock' => 1,
            'status' => 'active',
            'school' => 'none',
            'rarity' => 'new',
            'published_at' => now(),
        ]);

        $shipping = ShippingMethod::create(['name' => 'Courier', 'cost' => 50, 'estimated_days' => '2-4', 'is_active' => true]);
        $payment = PaymentMethod::create(['name' => 'Card', 'is_active' => true]);

        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 5]);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'first_name' => 'Triss',
            'last_name' => 'Merigold',
            'email' => 'triss@example.com',
            'address' => 'Temple Isle 3',
            'city' => 'Novigrad',
            'postal_code' => '11000',
            'region' => 'novigrad',
            'shipping_method_id' => $shipping->id,
            'payment_method_id' => $payment->id,
        ]);

        $response->assertRedirect(route('cart.show'));
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 1,
        ]);
    }
}
