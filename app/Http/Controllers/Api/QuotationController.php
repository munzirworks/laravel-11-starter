<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreQuotationRequest;
use App\Http\Requests\Api\UpdateQuotationRequest;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $quotations = Quotation::with(['customer', 'items.product'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'data' => $quotations,
            'message' => 'Quotations retrieved successfully.',
        ]);
    }

    public function store(StoreQuotationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $quotation = DB::transaction(function () use ($validated) {
            $docNo = $validated['doc_no'] ?? null;
            if (! $docNo) {
                $docNo = Quotation::generateDocNo();
            }

            $quotation = Quotation::create([
                'doc_no' => $docNo,
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'subtotal' => 0,
                'discount' => 0,
                'tax' => $validated['tax'] ?? 0,
                'total' => 0,
                'status' => $validated['status'] ?? 'draft',
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $subtotal = 0;
            $totalDiscount = 0;

            foreach ($validated['items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemDiscount = $item['discount'] ?? 0;
                $itemTotal = $itemSubtotal - $itemDiscount;

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $itemDiscount,
                    'subtotal' => $itemSubtotal,
                    'total' => $itemTotal,
                    'remarks' => $item['remarks'] ?? null,
                ]);

                $subtotal += $itemSubtotal;
                $totalDiscount += $itemDiscount;
            }

            $total = $subtotal - $totalDiscount + ($quotation->tax ?? 0);

            $quotation->update([
                'subtotal' => $subtotal,
                'discount' => $totalDiscount,
                'total' => $total,
            ]);

            return $quotation;
        });

        $quotation->load(['customer', 'items.product']);

        return response()->json([
            'data' => $quotation,
            'message' => 'Quotation created successfully.',
        ], 201);
    }

    public function show(Quotation $quotation): JsonResponse
    {
        $quotation->load(['customer', 'items.product']);

        return response()->json([
            'data' => $quotation,
            'message' => 'Quotation retrieved successfully.',
        ]);
    }

    public function update(UpdateQuotationRequest $request, Quotation $quotation): JsonResponse
    {
        $validated = $request->validated();

        $quotation = DB::transaction(function () use ($validated, $quotation) {
            $quotation->update([
                'doc_no' => $validated['doc_no'] ?? $quotation->doc_no,
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'tax' => $validated['tax'] ?? 0,
                'status' => $validated['status'] ?? $quotation->status,
                'remarks' => $validated['remarks'] ?? $quotation->remarks,
            ]);

            // remove old items and recreate
            $quotation->items()->delete();

            $subtotal = 0;
            $totalDiscount = 0;

            foreach ($validated['items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemDiscount = $item['discount'] ?? 0;
                $itemTotal = $itemSubtotal - $itemDiscount;

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $itemDiscount,
                    'subtotal' => $itemSubtotal,
                    'total' => $itemTotal,
                    'remarks' => $item['remarks'] ?? null,
                ]);

                $subtotal += $itemSubtotal;
                $totalDiscount += $itemDiscount;
            }

            $total = $subtotal - $totalDiscount + ($quotation->tax ?? 0);

            $quotation->update([
                'subtotal' => $subtotal,
                'discount' => $totalDiscount,
                'total' => $total,
            ]);

            return $quotation;
        });

        $quotation->load(['customer', 'items.product']);

        return response()->json([
            'data' => $quotation,
            'message' => 'Quotation updated successfully.',
        ]);
    }

    public function convertToSalesOrder(Quotation $quotation): JsonResponse
    {
        if ($quotation->converted_sales_order_id || $quotation->status === 'converted') {
            return response()->json([
                'message' => 'Quotation already converted.',
            ], 422);
        }

        $so = DB::transaction(function () use ($quotation) {
            $salesOrder = SalesOrder::create([
                'doc_no' => SalesOrder::generateDocNo(),
                'customer_id' => $quotation->customer_id,
                'order_date' => now()->toDateString(),
                'subtotal' => $quotation->subtotal,
                'discount' => $quotation->discount,
                'tax' => $quotation->tax,
                'total' => $quotation->total,
                'status' => 'draft',
                'remarks' => 'Converted from Quotation: ' . ($quotation->doc_no ?? $quotation->id),
                'created_by' => auth()->id(),
            ]);

            foreach ($quotation->items as $item) {
                SalesOrderItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'subtotal' => $item->subtotal,
                    'total' => $item->total,
                    'remarks' => $item->remarks,
                ]);
            }

            $quotation->update([
                'status' => 'converted',
                'converted_sales_order_id' => $salesOrder->id,
            ]);

            return $salesOrder;
        });

        $so->load(['customer', 'items.product']);

        return response()->json([
            'data' => $so,
            'message' => 'Quotation converted to Sales Order successfully.',
        ], 201);
    }
}
