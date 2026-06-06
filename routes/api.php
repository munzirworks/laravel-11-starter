<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\QuotationController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SalesOrderController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\PricingController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::apiResource('customers', CustomerController::class, ['only' => ['index', 'store', 'show', 'update']]);
    Route::apiResource('products', ProductController::class, ['only' => ['index', 'store', 'show', 'update']]);
    Route::apiResource('sales-orders', SalesOrderController::class, ['only' => ['index', 'store', 'show', 'update']]);
    Route::apiResource('invoices', InvoiceController::class, ['only' => ['index', 'store', 'show', 'update']]);
    Route::apiResource('payments', PaymentController::class, ['only' => ['index', 'store', 'show', 'update']]);
    Route::get('reports/dashboard', [ReportController::class, 'dashboard']);
    Route::get('reports/sales-summary', [ReportController::class, 'salesSummary']);
    Route::get('reports/outstanding', [ReportController::class, 'outstanding']);
    Route::get('reports/stock-summary', [ReportController::class, 'stockSummary']);
    Route::get('reports/top-products', [ReportController::class, 'topProducts']);
    Route::get('reports/top-customers', [ReportController::class, 'topCustomers']);
    Route::post('sales-orders/{salesOrder}/convert-to-invoice', [InvoiceController::class, 'convertToInvoice']);
    Route::apiResource('warehouses', WarehouseController::class, ['only' => ['index', 'store', 'show', 'update']]);
    Route::get('products/{product}/stock', [StockController::class, 'productStock']);
    Route::get('stocks', [StockController::class, 'index']);
    Route::post('stock-adjustments', [StockController::class, 'adjustment']);
    Route::apiResource('customer-product-prices', PricingController::class, ['only' => ['index', 'store', 'show', 'update']]);
    Route::post('pricing/calculate', [PricingController::class, 'calculate']);
    Route::apiResource('quotations', QuotationController::class, ['only' => ['index', 'store', 'show', 'update']]);
    Route::post('quotations/{quotation}/convert-to-sales-order', [QuotationController::class, 'convertToSalesOrder']);
});
