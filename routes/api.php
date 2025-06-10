<?php
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrdersController as ApiOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Customer endpoints
    Route::get('tables/{table:qr_code}', [CustomerController::class, 'getTable']);
    Route::get('menus', [CustomerController::class, 'getMenus']);
    Route::get('categories', [CustomerController::class, 'getCategories']);
    
    // Order endpoints
    Route::post('orders', [ApiOrderController::class, 'store']);
    Route::get('orders/{order}', [ApiOrderController::class, 'show']);
    Route::patch('orders/{order}/payment', [ApiOrderController::class, 'updatePayment']);
});