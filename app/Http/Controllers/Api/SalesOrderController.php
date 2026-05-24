<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSalesOrderRequest;
use App\Http\Requests\Api\UpdateSalesOrderRequest;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function index(): JsonResponse
    {
        $salesOrders = SalesOrder::with(['customer', 'items.product'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'data' => $salesOrders,
            'message' => 'Sales orders retrieved successfully.',
        ]);
    }

    public function store(StoreSalesOrderRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $salesOrder = SalesOrder::create([
                'doc_no' => $validated['doc_no'] ?? SalesOrder::generateDocNo(),
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'discount' => $validated['discount'] ?? 0,
                'tax' => $validated['tax'] ?? 0,
                'status' => $validated['status'] ?? 'draft',
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $subtotal = 0;

            foreach ($validated['items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemTotal = $itemSubtotal - ($item['discount'] ?? 0);

                $salesOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $itemSubtotal,
                    'total' => $itemTotal,
                    'remarks' => $item['remarks'] ?? null,
                ]);

                $subtotal += $itemSubtotal;
            }

            $salesOrder->subtotal = $subtotal;
            $salesOrder->total = $subtotal - $salesOrder->discount + $salesOrder->tax;
            $salesOrder->save();

            $salesOrder->load(['customer', 'items.product']);

            return response()->json([
                'data' => $salesOrder,
                'message' => 'Sales order created successfully.',
            ], 201);
        });
    }

    public function show(SalesOrder $salesOrder): JsonResponse
    {
        $salesOrder->load(['customer', 'items.product']);

        return response()->json([
            'data' => $salesOrder,
            'message' => 'Sales order retrieved successfully.',
        ]);
    }

    public function update(SalesOrder $salesOrder, UpdateSalesOrderRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($salesOrder, $request) {
            $validated = $request->validated();

            $salesOrder->update([
                'doc_no' => $validated['doc_no'] ?? $salesOrder->doc_no,
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'discount' => $validated['discount'] ?? 0,
                'tax' => $validated['tax'] ?? 0,
                'status' => $validated['status'] ?? $salesOrder->status,
                'remarks' => $validated['remarks'] ?? $salesOrder->remarks,
            ]);

            $salesOrder->items()->delete();

            $subtotal = 0;

            foreach ($validated['items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemTotal = $itemSubtotal - ($item['discount'] ?? 0);

                $salesOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $itemSubtotal,
                    'total' => $itemTotal,
                    'remarks' => $item['remarks'] ?? null,
                ]);

                $subtotal += $itemSubtotal;
            }

            $salesOrder->subtotal = $subtotal;
            $salesOrder->total = $subtotal - $salesOrder->discount + $salesOrder->tax;
            $salesOrder->save();

            $salesOrder->load(['customer', 'items.product']);

            return response()->json([
                'data' => $salesOrder,
                'message' => 'Sales order updated successfully.',
            ]);
        });
    }
}
