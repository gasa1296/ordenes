<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::apiResource('products', App\Http\Controllers\ProductController::class);
Route::apiResource('invoices', App\Http\Controllers\InvoiceController::class);
