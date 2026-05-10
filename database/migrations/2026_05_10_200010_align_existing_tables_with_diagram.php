<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $pg = DB::getDriverName() === 'pgsql';

        // ---- users ----
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('newsletter_opt_in')->default(false)->after('role');
            $table->softDeletes();
        });

        // ---- categories ----
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('image_url', 'image_path');
        });

        // ---- products: renames ----
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('description', 'full_description');
            $table->renameColumn('stock_quantity', 'stock');
            $table->renameColumn('average_rating', 'avg_rating');
            $table->renameColumn('total_reviews', 'review_count');
        });

        // Change column types (PostgreSQL-only, SQLite handles via recreation)
        if ($pg) {
            DB::statement('ALTER TABLE products ALTER COLUMN full_description SET NOT NULL');
            DB::statement("ALTER TABLE products ALTER COLUMN full_description SET DEFAULT ''");
            DB::statement('ALTER TABLE products ALTER COLUMN price TYPE integer USING price::integer');
            DB::statement('ALTER TABLE products ALTER COLUMN compare_price TYPE integer USING compare_price::integer');
            DB::statement('ALTER TABLE products ALTER COLUMN avg_rating TYPE decimal(4,2)');
            DB::statement('ALTER TABLE products ALTER COLUMN avg_rating DROP DEFAULT');
            DB::statement('ALTER TABLE products ALTER COLUMN avg_rating DROP NOT NULL');
        }

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_limited_edition')->default(false)->after('is_featured');
        });

        // ---- product_images ----
        Schema::table('product_images', function (Blueprint $table) {
            $table->renameColumn('url', 'path');
            $table->renameColumn('is_primary', 'is_main');
        });
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });

        // ---- product_specifications ----
        Schema::table('product_specifications', function (Blueprint $table) {
            $table->renameColumn('key', 'label');
        });
        Schema::table('product_specifications', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        // ---- reviews: restructure ----
        if ($pg) {
            DB::statement('ALTER TABLE reviews ALTER COLUMN user_id DROP NOT NULL');
        }
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('author_name')->after('user_id');
            $table->boolean('is_approved')->default(false)->after('body');
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'status']);
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['title', 'is_verified', 'status']);
            $table->softDeletes();
        });
        if ($pg) {
            DB::statement('ALTER TABLE reviews ALTER COLUMN rating TYPE smallint');
        }

        // ---- cart system: flatten ----
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });

        // ---- orders: restructure ----
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shipping_method_id')->constrained()->restrictOnDelete();
            $table->foreignId('payment_method_id')->constrained()->restrictOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number')->unique();
            $table->string('status')->default('pending');
            $table->string('ship_first_name');
            $table->string('ship_last_name');
            $table->string('ship_street');
            $table->string('ship_city');
            $table->string('ship_postal_code');
            $table->string('ship_region');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->text('notes')->nullable();
            $table->integer('subtotal');
            $table->integer('shipping_cost');
            $table->integer('discount')->default(0);
            $table->integer('total');
            $table->string('tracking_number')->nullable();
            $table->string('payment_status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['customer_email', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('product_sku');
            $table->integer('unit_price');
            $table->unsignedInteger('quantity');
            $table->integer('line_total');
        });
    }

    public function down(): void
    {
        // Destructive migration — restore from backup
    }
};
