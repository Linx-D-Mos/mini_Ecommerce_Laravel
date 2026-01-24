<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    
    Route::get('products/{product}/download', [ProductController::class, 'download']);
    Route::get('products/trashed', [ProductController::class, 'trashed']);
    Route::patch('products/{product}/restore', [ProductController::class, 'restore']);
    Route::apiResource('products', ProductController::class);
});
