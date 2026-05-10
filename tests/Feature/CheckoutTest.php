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

    public function test_guest_cannot_access_checkout(): void
    {
        $response = $this->get(route('checkout.show'));

        $response->assertUnauthorized();
    }

    public function test_guest_cannot_submit_checkout(): void
    {
        $response = $this->post(route('checkout.store'), [
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
            'stock_quantity' => 10,
            'status' => 'active',
            'school' => 'generic',
            'rarity' => 'common',
            'published_at' => now(),
        ]);

        $cart = Cart::create(['user_id' => $user->id, 'currency' => 'Crowns']);
        $cart->addProduct($product, 3);

        $this->actingAs($user)->post(route('checkout.store'), [
            'first_name' => 'Yennefer',
            'last_name' => 'of Vengerberg',
            'email' => 'yen@example.com',
            'address' => 'Aretuza',
            'city' => 'Gors Velen',
            'postal_code' => '20000',
            'region' => 'temeria',
            'delivery' => 'pickup',
            'payment' => 'card',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 7,
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
            'stock_quantity' => 1,
            'status' => 'active',
            'school' => 'generic',
            'rarity' => 'uncommon',
            'published_at' => now(),
        ]);

        $cart = Cart::create(['user_id' => $user->id, 'currency' => 'Crowns']);
        $cart->addProduct($product, 5);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'first_name' => 'Triss',
            'last_name' => 'Merigold',
            'email' => 'triss@example.com',
            'address' => 'Temple Isle 3',
            'city' => 'Novigrad',
            'postal_code' => '11000',
            'region' => 'novigrad',
            'delivery' => 'courier',
            'payment' => 'card',
        ]);

        $response->assertRedirect(route('cart.show'));
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 1,
        ]);
    }
}
