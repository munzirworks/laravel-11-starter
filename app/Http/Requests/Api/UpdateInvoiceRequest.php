<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $invoice = $this->route('invoice');
        $invoiceId = $invoice?->id ?? 0;

        return [
            'doc_no' => 'nullable|string|unique:invoices,doc_no,' . $invoiceId,
            'invoice_date' => 'sometimes|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.remarks' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_date.required' => 'Invoice date is required.',
            'items.min' => 'At least one item is required.',
            'items.*.product_id.required' => 'Product is required for each item.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.unit_price.required' => 'Unit price is required for each item.',
        ];
    }
}
