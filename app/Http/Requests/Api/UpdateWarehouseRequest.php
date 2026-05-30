<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $warehouseId = $this->route('warehouse')?->id ?? 0;

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('warehouses', 'code')->ignore($warehouseId)],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
