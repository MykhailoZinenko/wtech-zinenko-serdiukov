<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_account(): void
    {
        $this->get(route('account.profile'))->assertRedirect(route('login'));
    }

    public function test_user_can_view_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('account.profile'))
            ->assertOk()
            ->assertSee($user->email);
    }

    public function test_user_can_view_orders(): void
    {
        $user = User::factory()->create();
        $shipping = ShippingMethod::create(['name' => 'Post', 'cost' => 0, 'estimated_days' => '5-7', 'is_active' => true]);
        $payment = PaymentMethod::create(['name' => 'Cash', 'is_active' => true]);

        Order::create([
            'user_id' => $user->id,
            'shipping_method_id' => $shipping->id,
            'payment_method_id' => $payment->id,
            'order_number' => 'WW-TEST-001',
            'status' => 'pending',
            'ship_first_name' => 'Geralt',
            'ship_last_name' => 'of Rivia',
            'ship_street' => 'Kaer Morhen',
            'ship_city' => 'Kaedwen',
            'ship_postal_code' => '00000',
            'ship_region' => 'other',
            'customer_email' => $user->email,
            'subtotal' => 500,
            'shipping_cost' => 0,
            'total' => 500,
        ]);

        $this->actingAs($user)
            ->get(route('account.orders'))
            ->assertOk()
            ->assertSee('WW-TEST-001');
    }

    public function test_wishlist_toggle_adds_and_removes(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'sku' => 'TST-001',
            'price' => 100,
            'stock' => 5,
            'status' => 'active',
            'school' => 'none',
            'rarity' => 'common',
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->postJson(route('wishlist.toggle', $product->id))
            ->assertOk()
            ->assertJson(['wishlisted' => true]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user)
            ->postJson(route('wishlist.toggle', $product->id))
            ->assertOk()
            ->assertJson(['wishlisted' => false]);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_guest_orders_claimed_on_register(): void
    {
        $shipping = ShippingMethod::create(['name' => 'Post', 'cost' => 0, 'estimated_days' => '5-7', 'is_active' => true]);
        $payment = PaymentMethod::create(['name' => 'Cash', 'is_active' => true]);

        Order::create([
            'shipping_method_id' => $shipping->id,
            'payment_method_id' => $payment->id,
            'order_number' => 'WW-GUEST-001',
            'status' => 'pending',
            'ship_first_name' => 'Ciri',
            'ship_last_name' => 'of Cintra',
            'ship_street' => 'Temple Isle',
            'ship_city' => 'Novigrad',
            'ship_postal_code' => '11000',
            'ship_region' => 'novigrad',
            'customer_email' => 'ciri@example.com',
            'subtotal' => 300,
            'shipping_cost' => 50,
            'total' => 350,
        ]);

        $this->post(route('register'), [
            'first_name' => 'Ciri',
            'last_name' => 'of Cintra',
            'email' => 'ciri@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => '1',
        ]);

        $user = User::where('email', 'ciri@example.com')->first();
        $this->assertNotNull($user);
        $this->assertDatabaseHas('orders', [
            'order_number' => 'WW-GUEST-001',
            'user_id' => $user->id,
        ]);
    }
}
