<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    private const SAMPLES = [
        ['rating' => 5, 'body' => 'Slew a fiend on the way home from picking it up. Speaks for itself.'],
        ['rating' => 4, 'body' => 'Balance is true and the leather wrap fits the hand well. Edge held against three drowner skulls.'],
        ['rating' => 5, 'body' => 'Better than the dwarven smith in Vergen, and that\'s saying something. Will be back.'],
        ['rating' => 3, 'body' => 'Not legendary, but does the job. Wished the guard was a touch sturdier.'],
        ['rating' => 5, 'body' => 'Used it against a pack of nekkers near White Orchard. Walked away with all my limbs.'],
        ['rating' => 4, 'body' => 'Arrived in Novigrad in good order. Packaging was discreet — appreciated, given the contents.'],
        ['rating' => 2, 'body' => 'The blade arrived nicked and the scabbard was missing a stud. Returning for refund.'],
        ['rating' => 5, 'body' => 'Or so my brother-in-law (who once met a witcher) tells me. Either way, magnificent piece.'],
    ];

    public function run(): void
    {
        // Need at least a handful of users so reviewers vary
        $users = User::factory()->count(8)->create();
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $users->push($admin);
        }

        Product::with('reviews')->chunk(50, function ($products) use ($users) {
            foreach ($products as $product) {
                if ($product->reviews()->exists()) {
                    continue;
                }

                $count = random_int(2, 4);
                $samples = collect(self::SAMPLES)->shuffle()->take($count);
                $reviewers = $users->shuffle()->take($count);

                foreach ($samples as $i => $sample) {
                    Review::create([
                        'user_id' => $reviewers[$i]->id,
                        'product_id' => $product->id,
                        'rating' => $sample['rating'],
                        'body' => $sample['body'],
                        'author_name' => $reviewers[$i]->first_name . ' ' . $reviewers[$i]->last_name,
                        'is_approved' => true,
                    ]);
                }

                $avg = $product->reviews()->avg('rating');
                $total = $product->reviews()->count();
                $product->update([
                    'avg_rating' => round((float) $avg, 2),
                    'review_count' => $total,
                ]);
            }
        });
    }
}
