<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCustomerRequest;
use App\Http\Requests\Api\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    protected function ensureVisible(Customer $customer): void
    {
        $user = Auth::user();

        $isVisible = Customer::query()
            ->visibleTo($user)
            ->whereKey($customer->id)
            ->exists();

        if (! $isVisible) {
            abort(403, 'You do not have access to this customer.');
        }
    }

    public function index(): JsonResponse
    {
        $customers = Customer::query()
            ->visibleTo(Auth::user())
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
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'data' => $customer,
            'message' => 'Customer created successfully.',
        ], 201);
    }

    public function show(Customer $customer): JsonResponse
    {
        $this->ensureVisible($customer);

        return response()->json([
            'data' => $customer,
            'message' => 'Customer retrieved successfully.',
        ]);
    }

    public function update(Customer $customer, UpdateCustomerRequest $request): JsonResponse
    {
        $this->ensureVisible($customer);

        $customer->update($request->validated());

        return response()->json([
            'data' => $customer,
            'message' => 'Customer updated successfully.',
        ]);
    }
}
