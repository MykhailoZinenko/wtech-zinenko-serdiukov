<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Order;
use App\Models\User;
use App\Services\CartResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(private readonly CartResolver $cartResolver)
    {
    }

    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $guestSessionId = $request->session()->getId();

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'role' => 'customer',
        ]);

        Auth::login($user);
        $this->cartResolver->mergeGuestSessionInto($user, $guestSessionId);
        Order::where('customer_email', $user->email)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);
        $request->session()->regenerate();

        return redirect()->route('account.profile');
    }
}
