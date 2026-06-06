<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $payment = $this->route('payment');
        $paymentId = $payment?->id ?? 0;

        return [
            'doc_no' => 'nullable|string|unique:payments,doc_no,' . $paymentId,
            'payment_date' => 'sometimes|date',
            'amount' => 'sometimes|numeric|min:0.01',
            'payment_method' => 'sometimes|string',
            'reference_no' => 'nullable|string',
            'remarks' => 'nullable|string',
        ];
    }
}
