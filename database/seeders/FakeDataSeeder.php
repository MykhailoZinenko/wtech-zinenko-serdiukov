<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\ShippingMethod;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FakeDataSeeder extends Seeder
{
    private const REGIONS = ['novigrad', 'redania', 'temeria', 'nilfgaard', 'skellige', 'velen', 'toussaint', 'other'];

    private const REVIEW_BODIES = [
        'Slew a fiend on the way home from picking it up. Speaks for itself.',
        'Balance is true and the leather wrap fits the hand well. Edge held against three drowner skulls.',
        'Better than the dwarven smith in Vergen, and that\'s saying something. Will be back.',
        'Not legendary, but does the job. Wished the guard was a touch sturdier.',
        'Used it against a pack of nekkers near White Orchard. Walked away with all my limbs.',
        'Arrived in Novigrad in good order. Packaging was discreet — appreciated, given the contents.',
        'The blade arrived nicked and the scabbard was missing a stud. Returning for refund.',
        'Or so my brother-in-law (who once met a witcher) tells me. Either way, magnificent piece.',
        'Wore it through two blizzards in Skellige. Not a single seam gave way. Real craftsmanship.',
        'Light enough for a mage, sturdy enough for a witcher. Exactly what was promised.',
        'Used this oil on my last Noon Wraith contract. Two hits and the specter dissolved.',
        'Quality decent, delivery took longer than expected. Still, fair price for what you get.',
        'Gift for my nephew. He loves the design. Haven\'t tested in actual combat yet.',
        'Bought the full set. Each piece fits perfectly with the others. Feels like a second skin.',
        'The enchantment faded after three uses. Disappointed — expected more from this price range.',
        'Saved my life against a leshen. The armor absorbed an impact that would have killed me.',
        'Good for everyday carry, nothing special for monster hunting. Three stars for honesty.',
        'Brewed the potion exactly as described. Effects lasted two hours. Genuine quality ingredients.',
        'The crossbow fires true at 30 paces. Beyond that, accuracy drops. Fair for the price.',
        'Ordered two — one for me, one for the apprentice. Both arrived in perfect condition.',
    ];

    private const PRODUCT_ADJECTIVES = [
        'Temerian', 'Redanian', 'Nilfgaardian', 'Skellige', 'Kaedweni', 'Aedirnian',
        'Zerrikanian', 'Ofieri', 'Toussaint', 'Mahakaman', 'Elven', 'Dwarven',
        'Ancient', 'Enchanted', 'Reinforced', 'Masterwork', 'Runic', 'Hardened',
        'Ornate', 'Battle-Worn', 'Pristine', 'Cursed', 'Blessed', 'Lightweight',
    ];

    private const PRODUCT_NOUNS = [
        'swords' => ['Longsword', 'Shortsword', 'Claymore', 'Sabre', 'Cutlass', 'Falchion', 'Rapier', 'Blade'],
        'axes' => ['War Axe', 'Hatchet', 'Battleaxe', 'Cleaver', 'Broadaxe', 'Lumber Axe'],
        'crossbows' => ['Crossbow', 'Hand Crossbow', 'Siege Crossbow', 'Repeater', 'Arbalest'],
        'light-armor' => ['Gambeson', 'Leather Vest', 'Scout Jacket', 'Hide Tunic', 'Padded Coat'],
        'medium-armor' => ['Cuirass', 'Brigandine', 'Chain Shirt', 'Scale Armor', 'Hauberk'],
        'heavy-armor' => ['Plate Armor', 'Full Plate', 'Tower Shield', 'War Helm', 'Fortress Guard'],
        'sets' => ['Combat Set', 'Expedition Kit', 'Field Set', 'Patrol Gear', 'Campaign Bundle'],
        'potions' => ['Elixir', 'Decoction', 'Tincture', 'Brew', 'Philter', 'Draft'],
        'oils' => ['Blade Oil', 'Coating', 'Weapon Polish', 'Combat Grease', 'Silver Paste'],
        'bombs' => ['Bomb', 'Grenade', 'Flashbang', 'Cluster Charge', 'Smoke Device'],
        'trophies' => ['Trophy', 'Head', 'Fang', 'Skull', 'Claw', 'Horn'],
        'gwent-cards' => ['Card Pack', 'Hero Card', 'Deck Box', 'Collector Set', 'Foil Card'],
    ];

    public function run(): void
    {
        $faker = fake();

        $this->command->info('Creating extra products...');
        $this->seedProducts($faker);

        $this->command->info('Creating customers...');
        $customers = $this->seedCustomers(40, $faker);

        $this->command->info('Creating addresses...');
        $this->seedAddresses($customers, $faker);

        $this->command->info('Creating coupons...');
        $this->seedCoupons();

        $this->command->info('Creating orders (last 4 months, daily)...');
        $this->seedOrders($customers, $faker);

        $this->command->info('Creating additional reviews...');
        $this->seedReviews($customers, $faker);

        $this->command->info('Creating wishlists...');
        $this->seedWishlists($customers);

        $this->command->info('Creating newsletter subscribers...');
        $this->seedNewsletter($customers, $faker);

        $this->command->info('Recalculating product ratings...');
        $this->recalculateRatings();
    }

    private function seedProducts(\Faker\Generator $faker): void
    {
        if (Product::count() > 50) {
            return;
        }

        $categories = \App\Models\Category::all()->keyBy('slug');
        $skuCounter = Product::count() + 1;

        foreach (self::PRODUCT_NOUNS as $catSlug => $nouns) {
            $category = $categories->get($catSlug);
            if (!$category) {
                continue;
            }

            $toCreate = $faker->numberBetween(3, 6);
            for ($i = 0; $i < $toCreate; $i++) {
                $adj = $faker->randomElement(self::PRODUCT_ADJECTIVES);
                $noun = $faker->randomElement($nouns);
                $name = "$adj $noun";

                if (Product::where('name', $name)->exists()) {
                    continue;
                }

                $price = $faker->numberBetween(50, 8000);
                $hasCompare = $faker->boolean(25);
                $school = $faker->randomElement(Product::SCHOOLS);
                $rarity = $faker->randomElement(Product::RARITIES);

                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'sku' => 'WWE-' . str_pad($skuCounter++, 4, '0', STR_PAD_LEFT),
                    'short_description' => $faker->sentence(10),
                    'full_description' => $faker->paragraphs(2, true),
                    'price' => $price,
                    'compare_price' => $hasCompare ? (int) ($price * $faker->randomFloat(2, 1.1, 1.5)) : null,
                    'stock' => $faker->numberBetween(0, 50),
                    'low_stock_threshold' => 5,
                    'weight' => $faker->randomFloat(2, 0.1, 5.0),
                    'status' => $faker->randomElement(['active', 'active', 'active', 'draft']),
                    'school' => $school,
                    'rarity' => $rarity,
                    'is_featured' => $faker->boolean(15),
                    'published_at' => $faker->dateTimeBetween('-4 months'),
                ]);

                $base = "https://picsum.photos/seed/" . $product->slug;
                \App\Models\ProductImage::insert([
                    [
                        'product_id' => $product->id,
                        'path' => "$base/800/800",
                        'alt_text' => $name,
                        'sort_order' => 0,
                        'is_main' => true,
                        'created_at' => now(),
                    ],
                    [
                        'product_id' => $product->id,
                        'path' => "$base-2/800/800",
                        'alt_text' => "$name detail",
                        'sort_order' => 1,
                        'is_main' => false,
                        'created_at' => now(),
                    ],
                ]);

                $specCount = $faker->numberBetween(2, 5);
                $specLabels = $faker->randomElements(['Type', 'Damage', 'Weight', 'Material', 'Origin', 'Enchantment', 'Durability', 'Effect', 'Duration', 'Resistance'], $specCount);
                $sort = 0;
                foreach ($specLabels as $label) {
                    \App\Models\ProductSpecification::create([
                        'product_id' => $product->id,
                        'label' => $label,
                        'value' => $faker->words(rand(1, 3), true),
                        'sort_order' => $sort++,
                    ]);
                }
            }
        }
    }

    private function seedCustomers(int $count, \Faker\Generator $faker): \Illuminate\Support\Collection
    {
        $existing = User::where('role', 'customer')->get();
        if ($existing->count() >= $count) {
            return $existing;
        }

        $toCreate = $count - $existing->count();
        $users = collect();

        for ($i = 0; $i < $toCreate; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();

            $users->push(User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'password' => 'password',
                'role' => 'customer',
                'newsletter_opt_in' => $faker->boolean(40),
                'created_at' => $faker->dateTimeBetween('-4 months'),
            ]));
        }

        return $existing->merge($users);
    }

    private function seedAddresses(\Illuminate\Support\Collection $customers, \Faker\Generator $faker): void
    {
        if (Address::exists()) {
            return;
        }

        foreach ($customers->random(min(25, $customers->count())) as $customer) {
            $count = $faker->numberBetween(1, 2);
            for ($i = 0; $i < $count; $i++) {
                Address::create([
                    'user_id' => $customer->id,
                    'label' => $i === 0 ? 'Home' : 'Work',
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'phone' => $customer->phone,
                    'street' => $faker->streetAddress(),
                    'city' => $faker->city(),
                    'postal_code' => $faker->postcode(),
                    'region' => $faker->randomElement(self::REGIONS),
                    'is_default' => $i === 0,
                ]);
            }
        }
    }

    private function seedCoupons(): void
    {
        if (Coupon::exists()) {
            return;
        }

        $coupons = [
            ['code' => 'WITCHER10', 'description' => '10% off for guild members', 'type' => 'percentage', 'value' => 10, 'min_order_value' => 200, 'max_uses' => 100, 'times_used' => 34],
            ['code' => 'NOVIGRAD50', 'description' => '50 Crowns off in Novigrad', 'type' => 'fixed', 'value' => 50, 'min_order_value' => 300, 'max_uses' => 50, 'times_used' => 12],
            ['code' => 'SKELLIGE20', 'description' => '20% off Skellige gear', 'type' => 'percentage', 'value' => 20, 'min_order_value' => 500, 'max_uses' => 30, 'times_used' => 8],
            ['code' => 'SPRING100', 'description' => '100 Crowns off spring collection', 'type' => 'fixed', 'value' => 100, 'min_order_value' => 1000, 'max_uses' => 20, 'times_used' => 20, 'is_active' => false],
            ['code' => 'FIRSTORDER', 'description' => '15% off your first order', 'type' => 'percentage', 'value' => 15, 'min_order_value' => 0, 'max_uses' => 500, 'times_used' => 87],
        ];

        foreach ($coupons as $c) {
            Coupon::create(array_merge($c, [
                'valid_from' => now()->subMonths(3),
                'valid_until' => ($c['is_active'] ?? true) ? now()->addMonths(2) : now()->subWeek(),
                'is_active' => $c['is_active'] ?? true,
            ]));
        }
    }

    private function seedOrders(\Illuminate\Support\Collection $customers, \Faker\Generator $faker): void
    {
        if (Order::count() > 10) {
            return;
        }

        $products = Product::all();
        $shippingMethods = ShippingMethod::where('is_active', true)->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $coupons = Coupon::all();
        $admin = User::where('role', 'admin')->first();

        $statuses = ['processing', 'processing', 'shipped', 'delivered', 'delivered', 'delivered', 'cancelled'];

        for ($day = 120; $day >= 0; $day--) {
            $ordersToday = $faker->numberBetween(1, 3);

            for ($j = 0; $j < $ordersToday; $j++) {
            $isGuest = $faker->boolean(20);
            $customer = $isGuest ? null : $customers->random();
            $orderDate = now()->subDays($day)->setTime($faker->numberBetween(8, 22), $faker->numberBetween(0, 59));
            $shipping = $shippingMethods->random();
            $payment = $paymentMethods->random();
            $status = $faker->randomElement($statuses);
            $coupon = $faker->boolean(15) ? $coupons->random() : null;

            $itemCount = $faker->numberBetween(1, 4);
            $orderProducts = $products->random($itemCount);
            $subtotal = 0;
            $itemRows = [];

            foreach ($orderProducts as $product) {
                $qty = $faker->numberBetween(1, 3);
                $lineTotal = $product->price * $qty;
                $subtotal += $lineTotal;
                $itemRows[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $product->price,
                    'quantity' => $qty,
                    'line_total' => $lineTotal,
                ];
            }

            $discount = 0;
            if ($coupon) {
                $discount = $coupon->type === 'percentage'
                    ? (int) round($subtotal * $coupon->value / 100)
                    : $coupon->value;
                $discount = min($discount, $subtotal);
            }

            $total = $subtotal + $shipping->cost - $discount;

            $isCash = str_contains(strtolower($payment->name), 'cash');
            $paymentStatus = match ($status) {
                'cancelled' => $faker->randomElement(['refunded', 'failed']),
                default => $isCash ? 'pending' : 'paid',
            };

            $order = Order::create([
                'user_id' => $customer?->id,
                'shipping_method_id' => $shipping->id,
                'payment_method_id' => $payment->id,
                'coupon_id' => $coupon?->id,
                'order_number' => 'WW-' . $orderDate->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'status' => $status,
                'ship_first_name' => $customer?->first_name ?? $faker->firstName(),
                'ship_last_name' => $customer?->last_name ?? $faker->lastName(),
                'ship_street' => $faker->streetAddress(),
                'ship_city' => $faker->city(),
                'ship_postal_code' => $faker->postcode(),
                'ship_region' => $faker->randomElement(self::REGIONS),
                'customer_email' => $customer?->email ?? $faker->safeEmail(),
                'customer_phone' => $customer?->phone ?? $faker->phoneNumber(),
                'notes' => $faker->boolean(20) ? $faker->sentence() : null,
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping->cost,
                'discount' => $discount,
                'total' => $total,
                'tracking_number' => in_array($status, ['shipped', 'delivered']) ? 'TRK-' . Str::upper(Str::random(10)) : null,
                'payment_status' => $paymentStatus,
                'paid_at' => $paymentStatus === 'paid' ? $orderDate->copy()->addHours($faker->numberBetween(0, 2)) : null,
                'shipped_at' => in_array($status, ['shipped', 'delivered']) ? $orderDate->copy()->addDays($faker->numberBetween(1, 3)) : null,
                'delivered_at' => $status === 'delivered' ? $orderDate->copy()->addDays($faker->numberBetween(3, 7)) : null,
                'cancelled_at' => $status === 'cancelled' ? $orderDate->copy()->addHours($faker->numberBetween(1, 48)) : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            foreach ($itemRows as $row) {
                $order->items()->create($row);
            }

            $this->seedOrderHistory($order, $admin);

            if ($paymentStatus === 'paid') {
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method_id' => $payment->id,
                    'amount' => $total,
                    'status' => 'completed',
                    'transaction_reference' => 'PAY-' . Str::upper(Str::random(12)),
                    'created_at' => $order->paid_at,
                    'updated_at' => $order->paid_at,
                ]);
            }
            }
        }
    }

    private function seedOrderHistory(Order $order, ?User $admin): void
    {
        $transitions = match ($order->status) {
            'processing' => [
                ['processing', $order->created_at],
            ],
            'shipped' => [
                ['processing', $order->created_at],
                ['shipped', $order->shipped_at],
            ],
            'delivered' => [
                ['processing', $order->created_at],
                ['shipped', $order->shipped_at],
                ['delivered', $order->delivered_at],
            ],
            'cancelled' => [
                ['processing', $order->created_at],
                ['cancelled', $order->cancelled_at],
            ],
            default => [],
        };

        $prev = null;
        foreach ($transitions as [$status, $date]) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'changed_by_id' => $admin?->id,
                'old_status' => $prev,
                'new_status' => $status,
                'customer_notified' => rand(0, 1),
                'created_at' => $date,
            ]);
            $prev = $status;
        }
    }

    private function seedReviews(\Illuminate\Support\Collection $customers, \Faker\Generator $faker): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            $existingCount = $product->reviews()->count();
            $target = $faker->numberBetween(3, 8);
            $toAdd = max(0, $target - $existingCount);

            for ($i = 0; $i < $toAdd; $i++) {
                $isGuest = $faker->boolean(25);
                $customer = $isGuest ? null : $customers->random();

                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $customer?->id,
                    'author_name' => $customer
                        ? $customer->first_name . ' ' . $customer->last_name
                        : $faker->name(),
                    'rating' => $this->weightedRating(),
                    'body' => $faker->randomElement(self::REVIEW_BODIES),
                    'is_approved' => $faker->boolean(90),
                    'created_at' => $faker->dateTimeBetween('-4 months'),
                ]);
            }
        }
    }

    private function weightedRating(): int
    {
        $roll = rand(1, 100);
        if ($roll <= 5) return 1;
        if ($roll <= 15) return 2;
        if ($roll <= 30) return 3;
        if ($roll <= 60) return 4;
        return 5;
    }

    private function seedWishlists(\Illuminate\Support\Collection $customers): void
    {
        $products = Product::pluck('id');

        foreach ($customers->random(min(20, $customers->count())) as $customer) {
            $wishlistProducts = $products->random(rand(1, 5));
            foreach ($wishlistProducts as $productId) {
                Wishlist::firstOrCreate(
                    ['user_id' => $customer->id, 'product_id' => $productId],
                    ['created_at' => now()->subDays(rand(1, 90))],
                );
            }
        }
    }

    private function seedNewsletter(\Illuminate\Support\Collection $customers, \Faker\Generator $faker): void
    {
        if (NewsletterSubscriber::exists()) {
            return;
        }

        foreach ($customers->where('newsletter_opt_in', true) as $customer) {
            NewsletterSubscriber::create([
                'email' => $customer->email,
                'unsubscribe_token' => Str::random(32),
                'subscribed_at' => $customer->created_at->copy()->addDays(rand(0, 7)),
            ]);
        }

        for ($i = 0; $i < 8; $i++) {
            NewsletterSubscriber::create([
                'email' => $faker->unique()->safeEmail(),
                'unsubscribe_token' => Str::random(32),
                'subscribed_at' => $faker->dateTimeBetween('-4 months'),
            ]);
        }
    }

    private function recalculateRatings(): void
    {
        Product::all()->each(function (Product $product) {
            $approved = $product->reviews()->where('is_approved', true);
            $product->update([
                'avg_rating' => round((float) $approved->avg('rating'), 2),
                'review_count' => $approved->count(),
            ]);
        });
    }
}
