<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CalculatePriceRequest;
use App\Http\Requests\Api\StoreCustomerProductPriceRequest;
use App\Http\Requests\Api\UpdateCustomerProductPriceRequest;
use App\Models\CustomerProductPrice;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class PricingController extends Controller
{
    public function index(): JsonResponse
    {
        $prices = CustomerProductPrice::with(['customer', 'product'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'data' => $prices,
            'message' => 'Customer product prices retrieved successfully.',
        ]);
    }

    public function store(StoreCustomerProductPriceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $cpp = CustomerProductPrice::create([
            ...$data,
            'created_by' => auth()->id(),
        ]);

        $cpp->load(['customer', 'product']);

        return response()->json([
            'data' => $cpp,
            'message' => 'Customer product price created successfully.',
        ], 201);
    }

    public function show(CustomerProductPrice $customerProductPrice): JsonResponse
    {
        $customerProductPrice->load(['customer', 'product']);

        return response()->json([
            'data' => $customerProductPrice,
            'message' => 'Customer product price retrieved successfully.',
        ]);
    }

    public function update(CustomerProductPrice $customerProductPrice, UpdateCustomerProductPriceRequest $request): JsonResponse
    {
        $customerProductPrice->update($request->validated());
        $customerProductPrice->load(['customer', 'product']);

        return response()->json([
            'data' => $customerProductPrice,
            'message' => 'Customer product price updated successfully.',
        ]);
    }

    public function calculate(CalculatePriceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $customerId = $validated['customer_id'];
        $productId = $validated['product_id'];
        $quantity = $validated['quantity'];

        $cpp = CustomerProductPrice::where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->where('status', 'active')
            ->first();

        $product = Product::findOrFail($productId);

        if ($cpp) {
            $unitPrice = $cpp->price;
            $discountPercent = $cpp->discount_percent ?? 0;
        } else {
            $unitPrice = $product->price;
            $discountPercent = 0;
        }

        $subtotal = $quantity * $unitPrice;
        $discountAmount = $subtotal * ($discountPercent / 100);
        $total = $subtotal - $discountAmount;

        return response()->json([
            'data' => [
                'customer_id' => $customerId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => (float) $unitPrice,
                'discount_percent' => (float) $discountPercent,
                'discount_amount' => (float) number_format($discountAmount, 2, '.', ''),
                'subtotal' => (float) number_format($subtotal, 2, '.', ''),
                'total' => (float) number_format($total, 2, '.', ''),
            ],
            'message' => 'Price calculated successfully.',
        ]);
    }
}
