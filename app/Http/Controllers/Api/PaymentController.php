<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePaymentRequest;
use App\Http\Requests\Api\UpdatePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(): JsonResponse
    {
        $payments = Payment::with('invoice')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'data' => $payments,
            'message' => 'Payments retrieved successfully.',
        ]);
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validated();
            $invoice = Invoice::findOrFail($validated['invoice_id']);

            if ($validated['amount'] > $invoice->balance_amount) {
                return response()->json([
                    'message' => 'Payment amount cannot be greater than invoice balance.',
                ], 422);
            }

            $payment = Payment::create([
                'doc_no' => $validated['doc_no'] ?? Payment::generateDocNo(),
                'invoice_id' => $validated['invoice_id'],
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_no' => $validated['reference_no'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $this->updateInvoicePaymentStatus($invoice, $validated['amount']);

            $payment->load('invoice');

            return response()->json([
                'data' => $payment,
                'message' => 'Payment created successfully.',
            ], 201);
        });
    }

    public function show(Payment $payment): JsonResponse
    {
        $payment->load('invoice');

        return response()->json([
            'data' => $payment,
            'message' => 'Payment retrieved successfully.',
        ]);
    }

    public function update(UpdatePaymentRequest $request, Payment $payment): JsonResponse
    {
        return DB::transaction(function () use ($request, $payment) {
            $validated = $request->validated();
            $payment->load('invoice');
            $invoice = $payment->invoice;

            $newAmount = $validated['amount'] ?? $payment->amount;
            $allowedMax = $invoice->balance_amount + $payment->amount;

            if ($newAmount > $allowedMax) {
                return response()->json([
                    'message' => 'Payment amount cannot be greater than invoice balance.',
                ], 422);
            }

            $oldAmount = $payment->amount;

            $payment->update([
                'doc_no' => $validated['doc_no'] ?? $payment->doc_no,
                'payment_date' => $validated['payment_date'] ?? $payment->payment_date,
                'amount' => $newAmount,
                'payment_method' => $validated['payment_method'] ?? $payment->payment_method,
                'reference_no' => array_key_exists('reference_no', $validated) ? $validated['reference_no'] : $payment->reference_no,
                'remarks' => array_key_exists('remarks', $validated) ? $validated['remarks'] : $payment->remarks,
            ]);

            $delta = $newAmount - $oldAmount;
            $this->updateInvoicePaymentStatus($invoice, $delta);

            $payment->load('invoice');

            return response()->json([
                'data' => $payment,
                'message' => 'Payment updated successfully.',
            ]);
        });
    }

    protected function updateInvoicePaymentStatus(Invoice $invoice, float $amountDelta): void
    {
        $invoice->paid_amount = $invoice->paid_amount + $amountDelta;
        $invoice->balance_amount = $invoice->total - $invoice->paid_amount;

        if ($invoice->balance_amount <= 0) {
            $invoice->status = 'paid';
        } elseif ($invoice->paid_amount > 0 && $invoice->balance_amount > 0) {
            $invoice->status = 'partial';
        } else {
            $invoice->status = 'unpaid';
        }

        $invoice->save();
    }
}
