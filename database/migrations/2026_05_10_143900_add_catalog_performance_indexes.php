<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE INDEX IF NOT EXISTS products_status_category_id_index ON products (status, category_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS products_status_price_index ON products (status, price)');
        DB::statement('CREATE INDEX IF NOT EXISTS products_status_school_index ON products (status, school)');
        DB::statement('CREATE INDEX IF NOT EXISTS products_status_rarity_index ON products (status, rarity)');
        DB::statement('CREATE INDEX IF NOT EXISTS products_status_published_at_index ON products (status, published_at)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS products_status_published_at_index');
        DB::statement('DROP INDEX IF EXISTS products_status_rarity_index');
        DB::statement('DROP INDEX IF EXISTS products_status_school_index');
        DB::statement('DROP INDEX IF EXISTS products_status_price_index');
    }
};
