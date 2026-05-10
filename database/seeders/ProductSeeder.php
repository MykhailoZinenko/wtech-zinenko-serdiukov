<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $catalog = [
        // ── Swords & Daggers ──
        ['category' => 'swords', 'name' => 'Aerondight', 'school' => 'wolf', 'rarity' => 'legendary', 'price' => 4500, 'compare_price' => 5200, 'featured' => true,
            'short' => 'A blade of pure starlight, gifted by the Lady of the Lake.',
            'desc' => 'Forged from a single shard of meteoric silver, Aerondight strengthens with every honourable kill. Said to glow with the moon\'s blessing when drawn against the unworthy.',
            'specs' => ['Type' => 'Silver Sword', 'Damage' => '210–245', 'Weight' => '1.4 kg', 'Material' => 'Meteoric Silver', 'Origin' => 'Brokilon', 'Enchantment' => 'Charged']],
        ['category' => 'swords', 'name' => 'Feline Steel Sword', 'school' => 'cat', 'rarity' => 'rare', 'price' => 1850, 'featured' => true,
            'short' => 'A nimble steel blade favoured by Cat School witchers.',
            'desc' => 'Lighter and faster than its Wolf School counterpart, the Feline steel sword rewards quick footwork and precision strikes against human foes.',
            'specs' => ['Type' => 'Steel Sword', 'Damage' => '120–160', 'Weight' => '0.9 kg', 'Material' => 'Tempered Steel', 'Origin' => 'Stygga Castle']],
        ['category' => 'swords', 'name' => 'Ursine Silver Sword', 'school' => 'bear', 'rarity' => 'rare', 'price' => 2300,
            'short' => 'A heavy silver blade of the Bear School.',
            'desc' => 'Designed for sheer momentum, this slow but devastating silver blade is preferred by witchers who hunt larger beasts.',
            'specs' => ['Type' => 'Silver Sword', 'Damage' => '180–210', 'Weight' => '1.7 kg', 'Material' => 'Silver-iron alloy', 'Origin' => 'Skellige']],
        ['category' => 'swords', 'name' => 'Griffin Steel Sword', 'school' => 'griffin', 'rarity' => 'new', 'price' => 1450,
            'short' => 'A balanced steel sword of the Griffin School.',
            'desc' => 'Favoured by mages-turned-witchers, this blade is weighted to complement Sign casting in the heat of combat.',
            'specs' => ['Type' => 'Steel Sword', 'Damage' => '110–140', 'Weight' => '1.1 kg', 'Material' => 'Folded Steel', 'Origin' => 'Kaer Seren']],
        ['category' => 'swords', 'name' => 'Rusted Dagger', 'school' => 'none', 'rarity' => 'common', 'price' => 65,
            'short' => 'A common dagger, well-worn but serviceable.',
            'desc' => 'You won\'t slay a leshen with it, but it\'ll gut a drowner in a pinch.',
            'specs' => ['Type' => 'Dagger', 'Damage' => '15–22', 'Weight' => '0.3 kg', 'Material' => 'Iron']],

        // ── Axes & Hammers ──
        ['category' => 'axes', 'name' => 'Skellige War Axe', 'school' => 'none', 'rarity' => 'new', 'price' => 720,
            'short' => 'Forged in the smithies of Kaer Trolde.',
            'desc' => 'A two-handed axe with a heavy bronze-bound haft, beloved by raiders of the Isles. Will cleave shield and shoulder alike.',
            'specs' => ['Type' => 'Two-Handed Axe', 'Damage' => '95–125', 'Weight' => '2.3 kg', 'Material' => 'Iron + Oak']],
        ['category' => 'axes', 'name' => 'Mahakaman Hammer', 'school' => 'none', 'rarity' => 'rare', 'price' => 1340,
            'short' => 'A dwarven warhammer of unrivalled craftsmanship.',
            'desc' => 'Shaped from a single ingot of Mahakaman steel by the renowned smiths of the mountain holds. The head is engraved with old protective runes.',
            'specs' => ['Type' => 'Warhammer', 'Damage' => '140–175', 'Weight' => '3.1 kg', 'Material' => 'Mahakaman Steel']],

        // ── Crossbows ──
        ['category' => 'crossbows', 'name' => 'Zerrikanian Repeater', 'school' => 'none', 'rarity' => 'rare', 'price' => 1990, 'featured' => true,
            'short' => 'A six-bolt repeating crossbow from the Zerrikanian deserts.',
            'desc' => 'A marvel of southern engineering, capable of firing six bolts in rapid succession before reloading. Brought back by traders of the Trade Route.',
            'specs' => ['Type' => 'Crossbow', 'Damage' => '60 per bolt', 'Capacity' => '6 bolts', 'Material' => 'Yew + Brass']],
        ['category' => 'crossbows', 'name' => 'Hunter\'s Crossbow', 'school' => 'none', 'rarity' => 'common', 'price' => 320,
            'short' => 'A reliable single-shot crossbow.',
            'desc' => 'Standard issue for foresters and bounty hunters. Slow to reload but devastating at short range.',
            'specs' => ['Type' => 'Crossbow', 'Damage' => '80', 'Capacity' => '1 bolt', 'Material' => 'Ash + Steel']],

        // ── Light Armor ──
        ['category' => 'light-armor', 'name' => 'Cat School Gambeson', 'school' => 'cat', 'rarity' => 'new', 'price' => 980,
            'short' => 'Padded silk-lined armour for the swift.',
            'desc' => 'Light enough to permit acrobatics, yet quilted with double-stitched linen layers for surprising resilience.',
            'specs' => ['Type' => 'Light Chest', 'Armor' => '85', 'Weight' => '4 kg', 'Material' => 'Linen + Silk']],
        ['category' => 'light-armor', 'name' => 'Travelling Cloak', 'school' => 'none', 'rarity' => 'common', 'price' => 145,
            'short' => 'A weathered woollen cloak.',
            'desc' => 'Keeps the rain off, mostly. Has seen better roads.',
            'specs' => ['Type' => 'Cloak', 'Armor' => '12', 'Weight' => '1.5 kg', 'Material' => 'Wool']],

        // ── Medium Armor ──
        ['category' => 'medium-armor', 'name' => 'Griffin School Cuirass', 'school' => 'griffin', 'rarity' => 'rare', 'price' => 2150, 'compare_price' => 2400, 'featured' => true,
            'short' => 'A balanced cuirass enchanted to bolster Sign intensity.',
            'desc' => 'Etched with ancient sigils that channel the wearer\'s focus, the Griffin cuirass is the choice of witcher-mages who fight as much with the mind as the blade.',
            'specs' => ['Type' => 'Medium Chest', 'Armor' => '180', 'Weight' => '8 kg', 'Material' => 'Boiled Leather + Steel', 'Bonus' => '+15 Sign Intensity']],
        ['category' => 'medium-armor', 'name' => 'Manticore Trousers', 'school' => 'manticore', 'rarity' => 'rare', 'price' => 870,
            'short' => 'Reinforced leather trousers from Ofir.',
            'desc' => 'Studded with manticore-bone plates at the knees and thighs.',
            'specs' => ['Type' => 'Medium Legs', 'Armor' => '90', 'Weight' => '3 kg', 'Material' => 'Leather + Bone']],

        // ── Heavy Armor ──
        ['category' => 'heavy-armor', 'name' => 'Ursine Plate Cuirass', 'school' => 'bear', 'rarity' => 'legendary', 'price' => 6800, 'featured' => true,
            'short' => 'The famed plate of the Bear School, said to turn a dragon\'s breath.',
            'desc' => 'Forged from layered Skellige iron with bear-effigy bosses across the chest. Heavier than a smaller man, but it has stopped Wild Hunt blades.',
            'specs' => ['Type' => 'Heavy Chest', 'Armor' => '320', 'Weight' => '14 kg', 'Material' => 'Skelligan Iron', 'Bonus' => '+25 Toxicity Resistance']],
        ['category' => 'heavy-armor', 'name' => 'Nilfgaardian Officer Armor', 'school' => 'none', 'rarity' => 'rare', 'price' => 3200,
            'short' => 'Black-enamelled plate of the Empire.',
            'desc' => 'Looted, recovered, or… commissioned, this set bears the sun emblem of Imperator Emhyr. Polished to a mirror sheen.',
            'specs' => ['Type' => 'Heavy Chest', 'Armor' => '260', 'Weight' => '12 kg', 'Material' => 'Black Steel']],

        // ── Witcher Sets ──
        ['category' => 'sets', 'name' => 'Wolf School Gear (Full Set)', 'school' => 'wolf', 'rarity' => 'legendary', 'price' => 7990, 'compare_price' => 8800, 'featured' => true,
            'short' => 'The complete refurbished Wolf School armour, recovered from Kaer Morhen.',
            'desc' => 'Cuirass, gauntlets, trousers and boots — all bearing the howling wolf medallion. Painstakingly restored from the surviving pieces of the original training set.',
            'specs' => ['Type' => 'Full Set', 'Armor' => '420', 'Weight' => '18 kg', 'Material' => 'Steel + Hardened Leather', 'Bonus' => '+10 Adrenaline Gain']],

        // ── Potions & Decoctions ──
        ['category' => 'potions', 'name' => 'Swallow Potion', 'school' => 'none', 'rarity' => 'common', 'price' => 80,
            'short' => 'Accelerates vitality regeneration.',
            'desc' => 'A green herbal brew bottled at full moon. Bitter as regret, but it has saved many a witcher\'s life.',
            'specs' => ['Type' => 'Potion', 'Toxicity' => '40', 'Duration' => '30 s', 'Effect' => '+50 Vitality/sec']],
        ['category' => 'potions', 'name' => 'Cat Potion', 'school' => 'cat', 'rarity' => 'new', 'price' => 220,
            'short' => 'Grants vision in absolute darkness.',
            'desc' => 'A favourite for crypt-crawling and night hunts. Side effects include silver-eye reflection and mild paranoia.',
            'specs' => ['Type' => 'Potion', 'Toxicity' => '70', 'Duration' => '4 min', 'Effect' => 'Night Vision']],
        ['category' => 'potions', 'name' => 'Thunderbolt Potion', 'school' => 'none', 'rarity' => 'rare', 'price' => 480,
            'short' => 'Ignites the witcher\'s blood for greater attack power.',
            'desc' => 'A risky alchemical concoction. Boosts strength dramatically but courts toxic shock.',
            'specs' => ['Type' => 'Potion', 'Toxicity' => '100', 'Duration' => '45 s', 'Effect' => '+80 Attack']],

        // ── Blade Oils ──
        ['category' => 'oils', 'name' => 'Necrophage Oil', 'school' => 'none', 'rarity' => 'common', 'price' => 60,
            'short' => 'A blade-coating oil effective against the dead.',
            'desc' => 'Brewed from grave moss and burdock root. Particularly nasty to ghouls, alghouls, and rotfiends.',
            'specs' => ['Type' => 'Blade Oil', 'Charges' => '20 strikes', 'Effect' => '+25% damage vs. Necrophages']],
        ['category' => 'oils', 'name' => 'Specter Oil', 'school' => 'none', 'rarity' => 'new', 'price' => 180,
            'short' => 'Anoints silver blades against the incorporeal.',
            'desc' => 'Argentine compounds bound with consecrated water. Lets a silver blade bite into wraiths and noondays alike.',
            'specs' => ['Type' => 'Blade Oil', 'Charges' => '20 strikes', 'Effect' => '+25% damage vs. Specters']],

        // ── Bombs ──
        ['category' => 'bombs', 'name' => 'Dragon\'s Dream', 'school' => 'none', 'rarity' => 'rare', 'price' => 540, 'featured' => true,
            'short' => 'A flammable gas cloud bomb.',
            'desc' => 'Releases a volatile gas that ignites spectacularly when struck with a flame. Use sparingly, and not indoors.',
            'specs' => ['Type' => 'Bomb', 'Charges' => '3', 'Effect' => 'Gas + ignition']],
        ['category' => 'bombs', 'name' => 'Northern Wind', 'school' => 'none', 'rarity' => 'new', 'price' => 280,
            'short' => 'Freezes everything in its blast radius.',
            'desc' => 'A frost bomb favoured against vampire-kind and other heat-sensitive foes.',
            'specs' => ['Type' => 'Bomb', 'Charges' => '4', 'Effect' => 'Freeze']],
        ['category' => 'bombs', 'name' => 'Samum', 'school' => 'none', 'rarity' => 'common', 'price' => 95,
            'short' => 'A flash bomb that blinds opponents.',
            'desc' => 'Pulverised meteorite and powdered phosphorus. A sharp crack and a searing white light buys precious seconds.',
            'specs' => ['Type' => 'Bomb', 'Charges' => '5', 'Effect' => 'Blind']],

        // ── Herbs & Ingredients ──
        ['category' => 'herbs', 'name' => 'Mardroeme', 'school' => 'none', 'rarity' => 'rare', 'price' => 380,
            'short' => 'A rare hallucinogenic root.',
            'desc' => 'Used by sorceresses to trigger ritual transformations. Handle with gloves, and never near open flame.',
            'specs' => ['Type' => 'Herb', 'Quantity' => '50 g', 'Origin' => 'Brokilon Forest']],
        ['category' => 'herbs', 'name' => 'Celandine', 'school' => 'none', 'rarity' => 'common', 'price' => 25,
            'short' => 'A common roadside herb with mild healing properties.',
            'desc' => 'Yellow petals; bitter, faintly sour. Standard in many decoction recipes.',
            'specs' => ['Type' => 'Herb', 'Quantity' => '100 g']],
        ['category' => 'herbs', 'name' => 'Buckthorn Berries', 'school' => 'none', 'rarity' => 'common', 'price' => 18,
            'short' => 'A pouch of dried berries.',
            'desc' => 'Used in alchemy, and occasionally in cooking — though only if you don\'t mind a powerful purgative.',
            'specs' => ['Type' => 'Berry', 'Quantity' => '200 g']],

        // ── Monster Parts ──
        ['category' => 'monster-parts', 'name' => 'Foglet Teeth', 'school' => 'none', 'rarity' => 'new', 'price' => 220,
            'short' => 'A jar of foglet incisors.',
            'desc' => 'Six pristine teeth, each as sharp as a barber\'s razor. Required for several decoction recipes.',
            'specs' => ['Type' => 'Monster Trophy', 'Source' => 'Foglet']],
        ['category' => 'monster-parts', 'name' => 'Wyvern Hide', 'school' => 'none', 'rarity' => 'rare', 'price' => 1100,
            'short' => 'A square of cured wyvern hide.',
            'desc' => 'Tough, scaled, and naturally fire-resistant. The base material for the finest medium armour.',
            'specs' => ['Type' => 'Monster Trophy', 'Source' => 'Forktail / Wyvern']],
        ['category' => 'monster-parts', 'name' => 'Leshen Effigy', 'school' => 'none', 'rarity' => 'legendary', 'price' => 5400,
            'short' => 'A carved totem from a defeated leshen.',
            'desc' => 'Wood that still pulses with faint warmth. Druids will pay handsomely — others will warn you to bury it.',
            'specs' => ['Type' => 'Monster Trophy', 'Source' => 'Leshen']],

        // ── Gwent Cards ──
        ['category' => 'gwent', 'name' => 'Geralt of Rivia (Hero Card)', 'school' => 'none', 'rarity' => 'legendary', 'price' => 950, 'featured' => true,
            'short' => 'Legendary Northern Realms hero card.',
            'desc' => 'Strength 15. Immune to weather and special effects. The pride of any serious collector.',
            'specs' => ['Faction' => 'Northern Realms', 'Strength' => '15', 'Type' => 'Hero', 'Row' => 'Close Combat']],
        ['category' => 'gwent', 'name' => 'Yennefer of Vengerberg', 'school' => 'none', 'rarity' => 'legendary', 'price' => 880,
            'short' => 'Hero card with magical effects.',
            'desc' => 'Strength 7. Heals one of your discarded units. A queen-maker in skilled hands.',
            'specs' => ['Faction' => 'Northern Realms', 'Strength' => '7', 'Type' => 'Hero', 'Row' => 'Ranged Combat']],
        ['category' => 'gwent', 'name' => 'Roach', 'school' => 'none', 'rarity' => 'rare', 'price' => 240,
            'short' => 'A loyal companion, in card form.',
            'desc' => 'Strength 0. Triggers a Muster effect when paired with Geralt. Mostly stands on rooftops.',
            'specs' => ['Faction' => 'Neutral', 'Strength' => '0', 'Type' => 'Bronze', 'Ability' => 'Muster']],
        ['category' => 'gwent', 'name' => 'Vesemir', 'school' => 'wolf', 'rarity' => 'rare', 'price' => 320,
            'short' => 'The old wolf of Kaer Morhen.',
            'desc' => 'Strength 6. A reliable bronze unit and the heart of the Wolf School deck.',
            'specs' => ['Faction' => 'Northern Realms', 'Strength' => '6', 'Type' => 'Bronze']],
    ];

    public function run(): void
    {
        $skuCounter = 1000;

        foreach ($this->catalog as $entry) {
            $category = Category::where('slug', $entry['category'])->first();
            if (!$category) {
                continue;
            }

            $slug = \Illuminate\Support\Str::slug($entry['name']);
            $sku = sprintf('WWE-%04d', $skuCounter++);

            $publishedAt = Carbon::now()->subDays(random_int(0, 60));

            $product = Product::firstOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'name' => $entry['name'],
                    'sku' => $sku,
                    'short_description' => $entry['short'] ?? null,
                    'full_description' => $entry['desc'] ?? null,
                    'price' => $entry['price'],
                    'compare_price' => $entry['compare_price'] ?? null,
                    'stock' => random_int(3, 50),
                    'low_stock_threshold' => 5,
                    'weight' => null,
                    'status' => 'active',
                    'school' => $entry['school'] ?? 'none',
                    'rarity' => $entry['rarity'] ?? 'common',
                    'is_featured' => $entry['featured'] ?? false,
                    'published_at' => $publishedAt,
                ],
            );

            $this->seedImages($product);
            $this->seedSpecs($product, $entry['specs'] ?? []);
        }
    }

    private function seedImages(Product $product): void
    {
        if ($product->images()->exists()) {
            return;
        }

        $base = "https://picsum.photos/seed/{$product->slug}";

        ProductImage::insert([
            [
                'product_id' => $product->id,
                'path' => "{$base}/800/800",
                'alt_text' => $product->name,
                'sort_order' => 0,
                'is_main' => true,
                'created_at' => now(),
            ],
            [
                'product_id' => $product->id,
                'path' => "{$base}-2/800/800",
                'alt_text' => $product->name . ' detail',
                'sort_order' => 1,
                'is_main' => false,
                'created_at' => now(),
            ],
            [
                'product_id' => $product->id,
                'path' => "{$base}-3/800/800",
                'alt_text' => $product->name . ' alternate',
                'sort_order' => 2,
                'is_main' => false,
                'created_at' => now(),
            ],
        ]);
    }

    private function seedSpecs(Product $product, array $specs): void
    {
        if ($product->specifications()->exists()) {
            return;
        }

        $sort = 0;
        $rows = [];
        foreach ($specs as $key => $value) {
            $rows[] = [
                'product_id' => $product->id,
                'label' => $key,
                'value' => $value,
                'sort_order' => $sort++,
            ];
        }
        if ($rows) {
            ProductSpecification::insert($rows);
        }
    }
}
