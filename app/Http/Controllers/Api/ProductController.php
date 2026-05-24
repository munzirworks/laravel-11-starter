<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreProductRequest;
use App\Http\Requests\Api\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'data' => $products,
            'message' => 'Products retrieved successfully.',
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create([
            ...$request->validated(),
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'data' => $product,
            'message' => 'Product created successfully.',
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'data' => $product,
            'message' => 'Product retrieved successfully.',
        ]);
    }

    public function update(Product $product, UpdateProductRequest $request): JsonResponse
    {
        $product->update($request->validated());

        return response()->json([
            'data' => $product,
            'message' => 'Product updated successfully.',
        ]);
    }
}
