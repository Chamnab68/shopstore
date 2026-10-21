<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    ProductApiController,
    CategoryApiController,
    CartApiController,
    OrderApiController,
    ApiKeyController
};
use App\Http\Controllers\Admin\{DashboardController};

// ─── Public API (NO auth needed - for admin dashboard links) ──
Route::prefix('v1/public')->name('api.public.')->group(function () {
    Route::get('/products',                    [ProductApiController::class,  'index']);
    Route::get('/products/{slug}',             [ProductApiController::class,  'show']);
    Route::get('/categories',                  [CategoryApiController::class, 'index']);
    Route::get('/categories/{slug}/products',  [ProductApiController::class,  'byCategory']);
    Route::get('/orders',                      [OrderApiController::class,    'publicIndex']);
    Route::get('/orders/{id}',                 [OrderApiController::class,    'publicShow']);
});

// ─── Public API (requires X-API-KEY header or ?api_key=) ──────
Route::middleware('api.key')->prefix('v1')->name('api.')->group(function () {
    Route::get('/products',                    [ProductApiController::class,  'index']);
    Route::get('/products/{slug}',             [ProductApiController::class,  'show']);
    Route::get('/categories',                  [CategoryApiController::class, 'index']);
    Route::get('/categories/{slug}/products',  [ProductApiController::class,  'byCategory']);
});

// ─── Auth API ─────────────────────────────────────────────────
Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// ─── Authenticated API (Bearer Token) ─────────────────────────
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // Cart
    Route::get('/cart',         [CartApiController::class, 'index']);
    Route::post('/cart',        [CartApiController::class, 'add']);
    Route::patch('/cart/{id}',  [CartApiController::class, 'update']);
    Route::delete('/cart/{id}', [CartApiController::class, 'remove']);

    // Orders
    Route::get('/orders',                    [OrderApiController::class, 'index']);
    Route::post('/orders',                   [OrderApiController::class, 'store']);
    Route::get('/orders/{id}',               [OrderApiController::class, 'show']);
    Route::post('/orders/{id}/receipt',      [OrderApiController::class, 'uploadReceipt']);

    // API Keys
    Route::get('/api-keys',         [ApiKeyController::class, 'index']);
    Route::post('/api-keys',        [ApiKeyController::class, 'store']);
    Route::delete('/api-keys/{id}', [ApiKeyController::class, 'destroy']);
});
