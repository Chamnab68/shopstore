<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    ProductController,
    CartController,
    OrderController
};

use App\Http\Controllers\Admin\{
    DashboardController,
    ProductController as AdminProductController,
    CategoryController as AdminCategoryController,
    OrderController as AdminOrderController,
    ReportController,
    SettingController as AdminSettingController
};

//use App\Http\Controllers\BakongController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route::get('/checkout', [BakongController::class, 'showCheckout']);
// Route::post('/bakong/check-status', [BakongController::class, 'checkStatus'])->name('bakong.check');
/* --- Frontend Routes --- */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/category/{slug}', [ProductController::class, 'byCategory'])->name('products.category');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/orders', [OrderController::class, 'placeOrder'])->name('orders.place');
    Route::get('/orders', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/orders/{id}/payment', [OrderController::class, 'payment'])->name('orders.payment');
    Route::post('/orders/{id}/receipt', [OrderController::class, 'uploadReceipt'])->name('orders.receipt');
    Route::get('/orders/{id}/success', [OrderController::class, 'success'])->name('orders.success');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/process', [OrderController::class, 'process'])->name('orders.process');
});

require __DIR__.'/auth.php';

/* --- Admin Routes --- */
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Products Resource
    Route::resource('products', AdminProductController::class);

    // Categories
    Route::get('categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Orders
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{id}/approve', [AdminOrderController::class, 'approve'])->name('orders.approve');
    Route::post('orders/bulk-action', [AdminOrderController::class, 'bulkAction'])->name('orders.bulk');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export/{type}', [ReportController::class, 'exportPdf'])->name('export');
        Route::get('/stock', [ReportController::class, 'stockReport'])->name('stock');
    });

    // --- Admin Settings (Updated to PUT) ---
    Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::delete('settings/qr', [AdminSettingController::class, 'deleteQr'])->name('settings.delete-qr');

    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    

    
});