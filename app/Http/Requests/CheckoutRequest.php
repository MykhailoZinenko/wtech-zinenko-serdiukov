<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'shipping_method_id' => ['required', 'integer', 'exists:shipping_methods,id'],
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
        ];
    }
}
