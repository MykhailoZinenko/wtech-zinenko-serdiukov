<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartResolver
{
    /**
     * Return (creating if needed) the active cart for the current request.
     * Auth users get a user-scoped cart; guests get a session-scoped one.
     */
    public function resolve(): Cart
    {
        if ($user = Auth::user()) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            return $cart->load('items.product.primaryImage');
        }

        $sessionId = Session::getId();
        $cart = Cart::firstOrCreate(['session_id' => $sessionId, 'user_id' => null]);

        return $cart->load('items.product.primaryImage');
    }

    /**
     * Move a guest cart (identified by the previous session id) onto a logged-in user.
     * Called from LoginController/RegisterController right after auth.
     */
    public function mergeGuestSessionInto(User $user, ?string $guestSessionId): void
    {
        if (!$guestSessionId) {
            return;
        }

        $guestCart = Cart::where('session_id', $guestSessionId)->whereNull('user_id')->first();
        if (!$guestCart) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);
        $userCart->mergeFrom($guestCart);
    }

    public function itemCount(): int
    {
        return $this->resolve()->itemCount();
    }
}
