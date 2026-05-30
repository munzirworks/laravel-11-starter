<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $quotationId = $this->route('quotation')?->id ?? 0;

        return [
            'doc_no' => ['nullable', 'string', Rule::unique('quotations')->ignore($quotationId)],
            'customer_id' => ['required', 'exists:customers,id'],
            'quotation_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:quotation_date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.remarks' => ['nullable', 'string'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
