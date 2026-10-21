<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    ProductApiController,
    CategoryApiController,
    CartApiController,
    OrderApiController,
    ApiKeyController
};

// ─── Public API (requires X-API-KEY) ──────────────────────
Route::middleware('api.key')->prefix('v1')->name('api.')->group(function () {
    Route::get('/products', [ProductApiController::class, 'index'])->name('products.index');
    Route::get('/products/{slug}', [ProductApiController::class, 'show'])->name('products.show');
    Route::get('/categories', [CategoryApiController::class, 'index'])->name('categories.index');
    Route::get('/categories/{slug}/products', [ProductApiController::class, 'byCategory'])->name('products.by-category');
});

// ─── Auth API ─────────────────────────────────────────────
Route::prefix('v1/auth')->name('api.auth.')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

// ─── Authenticated API (Sanctum token) ────────────────────
Route::middleware('auth:sanctum')->prefix('v1')->name('api.')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');

    // Cart
    Route::get('/cart', [CartApiController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartApiController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{id}', [CartApiController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartApiController::class, 'remove'])->name('cart.remove');

    // Orders
    Route::get('/orders', [OrderApiController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderApiController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderApiController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/receipt', [OrderApiController::class, 'uploadReceipt'])->name('orders.receipt');

    // API Keys
    Route::get('/api-keys', [ApiKeyController::class, 'index'])->name('api-keys.index');
    Route::post('/api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::delete('/api-keys/{id}', [ApiKeyController::class, 'destroy'])->name('api-keys.destroy');
});
