<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreStockAdjustmentRequest;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(): JsonResponse
    {
        $balances = StockBalance::with(['warehouse', 'product'])
            ->orderByDesc('updated_at')
            ->paginate(15);

        return response()->json([
            'data' => $balances,
            'message' => 'Stock balances retrieved successfully.',
        ]);
    }

    public function productStock(Product $product): JsonResponse
    {
        $balances = StockBalance::with('warehouse')
            ->where('product_id', $product->id)
            ->get();

        return response()->json([
            'data' => $balances,
            'message' => 'Product stock balances retrieved successfully.',
        ]);
    }

    public function adjustment(StoreStockAdjustmentRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validated();
            $warehouseId = $validated['warehouse_id'];
            $productId = $validated['product_id'];
            $movementType = $validated['movement_type'];
            $quantity = $validated['quantity'];

            $stockBalance = StockBalance::firstOrCreate(
                ['warehouse_id' => $warehouseId, 'product_id' => $productId],
                ['quantity' => 0]
            );

            $newQuantity = $stockBalance->quantity;

            if ($movementType === 'adjustment_in') {
                $newQuantity += $quantity;
            } else {
                if ($stockBalance->quantity < $quantity) {
                    return response()->json([
                        'message' => 'Insufficient stock for adjustment_out.',
                    ], 422);
                }

                $newQuantity -= $quantity;
            }

            $stockBalance->quantity = $newQuantity;
            $stockBalance->save();

            $movement = StockMovement::create([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'balance_after' => $newQuantity,
                'reference_type' => $validated['reference_type'] ?? null,
                'reference_id' => $validated['reference_id'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $stockBalance->load(['warehouse', 'product']);
            $movement->load(['warehouse', 'product']);

            return response()->json([
                'data' => [
                    'balance' => $stockBalance,
                    'movement' => $movement,
                ],
                'message' => 'Stock adjustment completed successfully.',
            ]);
        });
    }
}
