<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Contracts\View\View;

class AccountController extends Controller
{
    public function profile(): View
    {
        return view('account.profile');
    }

    public function orders(): View
    {
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('account.orders', ['orders' => $orders]);
    }

    public function wishlist(): View
    {
        $items = Wishlist::where('user_id', auth()->id())
            ->with('product.primaryImage', 'product.category')
            ->latest()
            ->get()
            ->filter(fn ($w) => $w->product !== null);

        return view('account.wishlist', ['items' => $items]);
    }
}
