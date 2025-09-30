<?php

use Illuminate\Support\Facades\Route;

Route::apiResources([
    'products' => App\Http\Controllers\ProductController::class,
    'invoices' => App\Http\Controllers\InvoiceController::class,
]);
