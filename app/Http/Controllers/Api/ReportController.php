<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\StockBalance;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();
        $totalWarehouses = Warehouse::count();
        $totalQuotations = DB::table('quotations')->count();
        $totalSalesOrders = SalesOrder::count();
        $totalInvoices = Invoice::count();
        $totalPayments = Payment::count();
        $totalInvoiceAmount = Invoice::sum('total');
        $totalPaidAmount = Payment::sum('amount');
        $totalOutstandingAmount = Invoice::sum('balance_amount');

        return response()->json([
            'message' => 'Dashboard report retrieved successfully.',
            'data' => [
                'total_customers' => $totalCustomers,
                'total_products' => $totalProducts,
                'total_warehouses' => $totalWarehouses,
                'total_quotations' => $totalQuotations,
                'total_sales_orders' => $totalSalesOrders,
                'total_invoices' => $totalInvoices,
                'total_payments' => $totalPayments,
                'total_invoice_amount' => (string) number_format($totalInvoiceAmount, 2, '.', ''),
                'total_paid_amount' => (string) number_format($totalPaidAmount, 2, '.', ''),
                'total_outstanding_amount' => (string) number_format($totalOutstandingAmount, 2, '.', ''),
            ],
        ]);
    }

    public function salesSummary(Request $request): JsonResponse
    {
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $salesOrderQuery = SalesOrder::query();
        $invoiceQuery = Invoice::query();
        $paymentQuery = Payment::query();

        if ($fromDate) {
            $salesOrderQuery->where('order_date', '>=', $fromDate);
            $invoiceQuery->where('invoice_date', '>=', $fromDate);
            $paymentQuery->where('payment_date', '>=', $fromDate);
        }

        if ($toDate) {
            $salesOrderQuery->where('order_date', '<=', $toDate);
            $invoiceQuery->where('invoice_date', '<=', $toDate);
            $paymentQuery->where('payment_date', '<=', $toDate);
        }

        $totalSalesOrders = $salesOrderQuery->count();
        $salesOrderTotal = $salesOrderQuery->sum('total');
        $totalInvoices = $invoiceQuery->count();
        $invoiceTotal = $invoiceQuery->sum('total');
        $totalPaid = $paymentQuery->sum('amount');
        $totalOutstanding = $invoiceQuery->sum('balance_amount');

        return response()->json([
            'message' => 'Sales summary retrieved successfully.',
            'data' => [
                'total_sales_orders' => $totalSalesOrders,
                'sales_order_total' => (string) number_format($salesOrderTotal, 2, '.', ''),
                'total_invoices' => $totalInvoices,
                'invoice_total' => (string) number_format($invoiceTotal, 2, '.', ''),
                'total_paid' => (string) number_format($totalPaid, 2, '.', ''),
                'total_outstanding' => (string) number_format($totalOutstanding, 2, '.', ''),
            ],
        ]);
    }

    public function outstanding(): JsonResponse
    {
        $invoices = Invoice::with('customer')
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderByDesc('invoice_date')
            ->get()
            ->map(function ($invoice) {
                return [
                    'invoice_id' => $invoice->id,
                    'doc_no' => $invoice->doc_no,
                    'customer' => [
                        'id' => $invoice->customer->id,
                        'name' => $invoice->customer->name,
                        'code' => $invoice->customer->code,
                    ],
                    'invoice_total' => (string) number_format($invoice->total, 2, '.', ''),
                    'paid_amount' => (string) number_format($invoice->paid_amount, 2, '.', ''),
                    'balance_amount' => (string) number_format($invoice->balance_amount, 2, '.', ''),
                    'invoice_date' => $invoice->invoice_date?->toDateString(),
                    'due_date' => $invoice->due_date?->toDateString(),
                    'status' => $invoice->status,
                ];
            });

        return response()->json([
            'message' => 'Outstanding invoices retrieved successfully.',
            'data' => $invoices,
        ]);
    }

    public function stockSummary(): JsonResponse
    {
        $balances = StockBalance::with(['product', 'warehouse'])
            ->orderByDesc('quantity')
            ->get()
            ->map(function ($balance) {
                return [
                    'product' => [
                        'id' => $balance->product->id,
                        'name' => $balance->product->name,
                        'code' => $balance->product->code,
                    ],
                    'warehouse' => [
                        'id' => $balance->warehouse->id,
                        'name' => $balance->warehouse->name,
                        'code' => $balance->warehouse->code,
                    ],
                    'quantity' => (string) number_format($balance->quantity, 2, '.', ''),
                ];
            });

        return response()->json([
            'message' => 'Stock summary retrieved successfully.',
            'data' => $balances,
        ]);
    }

    public function topProducts(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 10);
        $limit = $limit > 0 ? $limit : 10;

        $products = InvoiceItem::selectRaw('product_id, SUM(quantity) as total_quantity, SUM(total) as total_sales')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $product = Product::find($item->product_id);

                return [
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'code' => $product->code,
                    ],
                    'total_quantity' => (string) number_format($item->total_quantity, 2, '.', ''),
                    'total_sales' => (string) number_format($item->total_sales, 2, '.', ''),
                ];
            });

        return response()->json([
            'message' => 'Top products retrieved successfully.',
            'data' => $products,
        ]);
    }

    public function topCustomers(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 10);
        $limit = $limit > 0 ? $limit : 10;

        $customers = Invoice::selectRaw('customer_id, SUM(total) as invoice_total')
            ->groupBy('customer_id')
            ->orderByDesc('invoice_total')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $customer = Customer::find($item->customer_id);

                return [
                    'customer' => [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'code' => $customer->code,
                    ],
                    'invoice_total' => (string) number_format($item->invoice_total, 2, '.', ''),
                ];
            });

        return response()->json([
            'message' => 'Top customers retrieved successfully.',
            'data' => $customers,
        ]);
    }
}
