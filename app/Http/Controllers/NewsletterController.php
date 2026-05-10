<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        NewsletterSubscriber::firstOrCreate(
            ['email' => $request->input('email')],
            [
                'unsubscribe_token' => Str::random(32),
                'subscribed_at' => now(),
            ],
        );

        return back()->with('newsletter_success', 'You have been subscribed.');
    }
}
