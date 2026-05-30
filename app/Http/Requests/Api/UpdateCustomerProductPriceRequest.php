<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerProductPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cpp = $this->route('customer_product_price') ?? $this->route('customerProductPrice');
        $id = $cpp?->id ?? 0;

        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'product_id' => [
                'required',
                'exists:products,id',
                Rule::unique('customer_product_prices')
                    ->where('customer_id', $this->input('customer_id'))
                    ->where('product_id', $this->input('product_id'))
                    ->ignore($id),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
