<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SalesOrderController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::apiResource('customers', CustomerController::class, ['only' => ['index', 'store', 'show', 'update']]);
    Route::apiResource('products', ProductController::class, ['only' => ['index', 'store', 'show', 'update']]);
    Route::apiResource('sales-orders', SalesOrderController::class, ['only' => ['index', 'store', 'show', 'update']]);
    Route::apiResource('warehouses', WarehouseController::class, ['only' => ['index', 'store', 'show', 'update']]);
    Route::get('products/{product}/stock', [StockController::class, 'productStock']);
    Route::get('stocks', [StockController::class, 'index']);
    Route::post('stock-adjustments', [StockController::class, 'adjustment']);
});
