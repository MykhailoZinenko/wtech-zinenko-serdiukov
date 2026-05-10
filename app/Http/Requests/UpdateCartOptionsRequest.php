<?php

namespace App\Http\Requests;

use App\Services\OrderOptionService;
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
            'delivery' => ['required', 'string', Rule::in(array_keys(OrderOptionService::DELIVERY_OPTIONS))],
            'payment' => ['required', 'string', Rule::in(array_keys(OrderOptionService::PAYMENT_OPTIONS))],
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
