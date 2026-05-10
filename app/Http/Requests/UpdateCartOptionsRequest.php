<?php

namespace App\Http\Requests;

use App\Http\Controllers\CartController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCartOptionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery' => ['required', 'string', Rule::in(array_keys(CartController::DELIVERY_OPTIONS))],
            'payment' => ['required', 'string', Rule::in(array_keys(CartController::PAYMENT_OPTIONS))],
        ];
    }

    public function deliveryMethod(): string
    {
        return (string) $this->input('delivery');
    }

    public function paymentMethod(): string
    {
        return (string) $this->input('payment');
    }
}
