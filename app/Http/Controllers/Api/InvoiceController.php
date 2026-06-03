<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreInvoiceRequest;
use App\Http\Requests\Api\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\SalesOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(): JsonResponse
    {
        $invoices = Invoice::with(['customer', 'items.product'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'data' => $invoices,
            'message' => 'Invoices retrieved successfully.',
        ]);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $invoice = Invoice::create([
                'doc_no' => $validated['doc_no'] ?? Invoice::generateDocNo(),
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'] ?? null,
                'discount' => $validated['discount'] ?? 0,
                'tax' => $validated['tax'] ?? 0,
                'status' => 'unpaid',
                'paid_amount' => 0,
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $subtotal = 0;

            foreach ($validated['items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemTotal = $itemSubtotal - ($item['discount'] ?? 0);

                $invoice->items()->create([
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

            $invoice->subtotal = $subtotal;
            $invoice->total = $subtotal - $invoice->discount + $invoice->tax;
            $invoice->balance_amount = $invoice->total - $invoice->paid_amount;
            $invoice->save();

            $invoice->load(['customer', 'items.product']);

            return response()->json([
                'data' => $invoice,
                'message' => 'Invoice created successfully.',
            ], 201);
        });
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load(['customer', 'items.product']);

        return response()->json([
            'data' => $invoice,
            'message' => 'Invoice retrieved successfully.',
        ]);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        return DB::transaction(function () use ($request, $invoice) {
            $validated = $request->validated();

            $invoice->update([
                'doc_no' => $validated['doc_no'] ?? $invoice->doc_no,
                'invoice_date' => $validated['invoice_date'] ?? $invoice->invoice_date,
                'due_date' => $validated['due_date'] ?? $invoice->due_date,
                'discount' => $validated['discount'] ?? $invoice->discount,
                'tax' => $validated['tax'] ?? $invoice->tax,
                'remarks' => $validated['remarks'] ?? $invoice->remarks,
            ]);

            // Start with existing subtotal; if items provided, replace and recalc
            $subtotal = $invoice->subtotal;

            if (isset($validated['items'])) {
                $invoice->items()->delete();

                $subtotal = 0;

                foreach ($validated['items'] as $item) {
                    $itemSubtotal = $item['quantity'] * $item['unit_price'];
                    $itemTotal = $itemSubtotal - ($item['discount'] ?? 0);

                    $invoice->items()->create([
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

                $invoice->subtotal = $subtotal;
            }

            // Always recalculate totals and balance after any update
            $invoice->total = $subtotal - ($invoice->discount ?? 0) + ($invoice->tax ?? 0);
            $invoice->balance_amount = $invoice->total - ($invoice->paid_amount ?? 0);
            $invoice->save();

            $invoice->load(['customer', 'items.product']);

            return response()->json([
                'data' => $invoice,
                'message' => 'Invoice updated successfully.',
            ]);
        });
    }

    public function convertToInvoice(SalesOrder $salesOrder): JsonResponse
    {
        // Check if invoice already exists for this sales order
        $existingInvoice = Invoice::where('sales_order_id', $salesOrder->id)->first();

        if ($existingInvoice) {
            return response()->json([
                'message' => 'Invoice already exists for this sales order.',
            ], 409);
        }

        return DB::transaction(function () use ($salesOrder) {
            // Create invoice from sales order
            $invoice = Invoice::create([
                'doc_no' => Invoice::generateDocNo(),
                'customer_id' => $salesOrder->customer_id,
                'sales_order_id' => $salesOrder->id,
                'invoice_date' => now()->toDateString(),
                'due_date' => null,
                'subtotal' => $salesOrder->subtotal,
                'discount' => $salesOrder->discount,
                'tax' => $salesOrder->tax,
                'total' => $salesOrder->total,
                'status' => 'unpaid',
                'paid_amount' => 0,
                'remarks' => 'Converted from Sales Order ' . $salesOrder->doc_no,
                'created_by' => auth()->id(),
            ]);

            // Copy items from sales order
            foreach ($salesOrder->items as $soItem) {
                $invoice->items()->create([
                    'product_id' => $soItem->product_id,
                    'quantity' => $soItem->quantity,
                    'unit_price' => $soItem->unit_price,
                    'discount' => $soItem->discount,
                    'subtotal' => $soItem->subtotal,
                    'total' => $soItem->total,
                    'remarks' => $soItem->remarks,
                ]);
            }

            // Calculate balance amount
            $invoice->balance_amount = $invoice->total - $invoice->paid_amount;
            $invoice->save();

            $invoice->load(['customer', 'items.product']);

            return response()->json([
                'data' => $invoice,
                'message' => 'Invoice created from sales order successfully.',
            ], 201);
        });
    }
}
