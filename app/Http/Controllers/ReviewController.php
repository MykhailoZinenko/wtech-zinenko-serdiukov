<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_if($product->status !== 'active', 404);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'body' => 'required|string|min:10|max:2000',
            'author_name' => 'required_without:_auth|string|max:100',
        ], [
            'rating.required' => 'Please select a star rating.',
            'body.required' => 'Please write your review.',
            'body.min' => 'Your review must be at least 10 characters.',
            'author_name.required_without' => 'Please enter your name.',
        ]);

        $user = auth()->user();

        Review::create([
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'author_name' => $user?->first_name
                ? "{$user->first_name} {$user->last_name}"
                : $validated['author_name'],
            'rating' => $validated['rating'],
            'body' => $validated['body'],
            'is_approved' => true,
        ]);

        $product->update([
            'avg_rating' => $product->reviews()->avg('rating'),
            'review_count' => $product->reviews()->count(),
        ]);

        return back()->with('review_success', 'Your review has been posted.');
    }
}
