<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'unique:customers,code', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string', 'max:500'],
            'area' => ['nullable', 'string', 'max:100'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
