<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCustomerRequest;
use App\Http\Requests\Api\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function index(): JsonResponse
    {
        $customers = Customer::query()
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'data' => $customers,
            'message' => 'Customers retrieved successfully.',
        ]);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create([
            ...$request->validated(),
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'data' => $customer,
            'message' => 'Customer created successfully.',
        ], 201);
    }

    public function show(Customer $customer): JsonResponse
    {
        return response()->json([
            'data' => $customer,
            'message' => 'Customer retrieved successfully.',
        ]);
    }

    public function update(Customer $customer, UpdateCustomerRequest $request): JsonResponse
    {
        $customer->update($request->validated());

        return response()->json([
            'data' => $customer,
            'message' => 'Customer updated successfully.',
        ]);
    }
}
