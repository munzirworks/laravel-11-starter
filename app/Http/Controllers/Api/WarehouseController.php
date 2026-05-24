<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreWarehouseRequest;
use App\Http\Requests\Api\UpdateWarehouseRequest;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    public function index(): JsonResponse
    {
        $warehouses = Warehouse::query()
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'data' => $warehouses,
            'message' => 'Warehouses retrieved successfully.',
        ]);
    }

    public function store(StoreWarehouseRequest $request): JsonResponse
    {
        $warehouse = Warehouse::create([
            ...$request->validated(),
            'status' => $request->validated()['status'] ?? 'active',
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'data' => $warehouse,
            'message' => 'Warehouse created successfully.',
        ], 201);
    }

    public function show(Warehouse $warehouse): JsonResponse
    {
        return response()->json([
            'data' => $warehouse,
            'message' => 'Warehouse retrieved successfully.',
        ]);
    }

    public function update(Warehouse $warehouse, UpdateWarehouseRequest $request): JsonResponse
    {
        $warehouse->update($request->validated());

        return response()->json([
            'data' => $warehouse,
            'message' => 'Warehouse updated successfully.',
        ]);
    }
}
