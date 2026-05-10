<?php

namespace App\Http\Requests;

use App\Http\Controllers\CartController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'postal_code' => ['required', 'string', 'max:20'],
            'region' => ['required', 'string', 'in:novigrad,redania,temeria,nilfgaard,skellige,velen,toussaint,other'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'delivery' => ['required', 'string', Rule::in(array_keys(CartController::DELIVERY_OPTIONS))],
            'payment' => ['required', 'string', Rule::in(array_keys(CartController::PAYMENT_OPTIONS))],
        ];
    }
}
