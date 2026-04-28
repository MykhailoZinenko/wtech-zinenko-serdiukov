<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Tree of categories: parent slug => [name, [children...]].
     */
    private const TREE = [
        ['name' => 'Weapons', 'slug' => 'weapons', 'children' => [
            ['name' => 'Swords & Daggers', 'slug' => 'swords'],
            ['name' => 'Axes & Hammers', 'slug' => 'axes'],
            ['name' => 'Crossbows', 'slug' => 'crossbows'],
        ]],
        ['name' => 'Armor & Gear', 'slug' => 'armor', 'children' => [
            ['name' => 'Light Armor', 'slug' => 'light-armor'],
            ['name' => 'Medium Armor', 'slug' => 'medium-armor'],
            ['name' => 'Heavy Armor', 'slug' => 'heavy-armor'],
            ['name' => 'Witcher Sets', 'slug' => 'sets'],
        ]],
        ['name' => 'Alchemy & Potions', 'slug' => 'alchemy', 'children' => [
            ['name' => 'Potions & Decoctions', 'slug' => 'potions'],
            ['name' => 'Blade Oils', 'slug' => 'oils'],
            ['name' => 'Bombs', 'slug' => 'bombs'],
            ['name' => 'Herbs & Ingredients', 'slug' => 'herbs'],
        ]],
        ['name' => 'Monster Parts & Trophies', 'slug' => 'monster-parts'],
        ['name' => 'Gwent Cards', 'slug' => 'gwent'],
    ];

    public function run(): void
    {
        $sortRoot = 0;
        foreach (self::TREE as $node) {
            $parent = Category::firstOrCreate(
                ['slug' => $node['slug']],
                ['name' => $node['name'], 'sort_order' => $sortRoot++, 'is_active' => true],
            );

            $sortChild = 0;
            foreach ($node['children'] ?? [] as $child) {
                Category::firstOrCreate(
                    ['slug' => $child['slug']],
                    [
                        'name' => $child['name'],
                        'parent_id' => $parent->id,
                        'sort_order' => $sortChild++,
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
