<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{HomeController, ProductController, CartController, OrderController, BakongController};
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\{DashboardController, ReportController, AdminAnalyticsController, LoyaltyController as AdminLoyaltyController};
use App\Http\Controllers\Admin\{ProductController as AdminProductController, CategoryController as AdminCategoryController, OrderController as AdminOrderController, SettingController as AdminSettingController, ReferralProgramController};
use App\Models\Order;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\TierController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ------------------ Guest / Public & Auth Custom Routes ------------------
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/category/{slug}', [ProductController::class, 'byCategory'])->name('products.category');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

// Payment Gateway Callback & Simulation Routes
Route::get('/bakong/check-status', [BakongController::class, 'checkStatus'])->name('bakong.check');


// ------------------ Authenticated Customer Routes ------------------
Route::middleware(['auth'])->group(function () {
    Route::get('/rewards', [HomeController::class, 'rewards'])->name('rewards'); 
    Route::post('/rewards/{id}/redeem', [HomeController::class, 'redeemReward'])->name('rewards.redeem');
    
    // Checkout Routes
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout/place', [OrderController::class, 'placeOrder'])->name('orders.place');

    // Customer Order Management
    Route::get('/orders', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    // ធានាថាមានការដាក់ ->name('checkout.index') ត្រឹមត្រូវ
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout.index');
    // Payment & Upload Routes
    Route::get('/orders/{id}/payment', [OrderController::class, 'payment'])->name('orders.payment');
    
    // 👇 បន្ថែម Route នេះសម្រាប់ Bakong Payment Page ທີ່ខ្វះខាត
    Route::get('/orders/{id}/bakong-payment', [OrderController::class, 'bakongPayment'])->name('orders.bakong-payment');

    Route::post('/orders/{id}/simulate-paid', [OrderController::class, 'simulatePaid'])->name('orders.simulate-paid');
    Route::match(['get', 'post'], '/orders/{id}/process', [OrderController::class, 'process'])->name('orders.process');
    Route::post('/orders/{id}/receipt', [OrderController::class, 'uploadReceipt'])->name('orders.receipt');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});


// ------------------ Auth Routes ------------------
require __DIR__.'/auth.php';


// ------------------ Admin Dashboard Routes (Prefix: admin) ------------------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Main Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Users Management Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/users/{id}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Activity Log Route
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    
    // Modules Resource & Customs
    Route::resource('products', AdminProductController::class);
    
    Route::get('categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{id}/approve', [AdminOrderController::class, 'approve'])->name('orders.approve');
    Route::post('orders/bulk-action', [AdminOrderController::class, 'bulkAction'])->name('orders.bulk');
    
    // Admin Analytics & Reports
    Route::get('/analytics/loyalty', [AdminAnalyticsController::class, 'loyaltyAnalytics'])->name('analytics.loyalty');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sales', [ReportController::class, 'salesSummary'])->name('reports.sales');
    Route::get('reports/revenue-category', [ReportController::class, 'revenueByCategory'])->name('reports.revenue-category');
    Route::get('reports/products', [ReportController::class, 'topProducts'])->name('reports.products');
    Route::get('reports/customers', [ReportController::class, 'customerReport'])->name('reports.customers');
    Route::get('reports/order-status', [ReportController::class, 'orderStatus'])->name('reports.order-status');
    Route::get('reports/payment', [ReportController::class, 'paymentReport'])->name('reports.payment');
    Route::get('reports/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
    Route::get('reports/monthly-revenue', [ReportController::class, 'monthlyTrend'])->name('reports.monthly-revenue');
    Route::get('reports/customer-retention', [ReportController::class, 'customerRetention'])->name('reports.customer-retention');
    Route::get('reports/delivery', [ReportController::class, 'deliveryPerformance'])->name('reports.delivery');
    Route::get('reports/export/{type}', [ReportController::class, 'exportPdf'])->name('reports.export');

    Route::get('/tiers', [TierController::class, 'index'])->name('tiers.index');
    Route::get('/tiers/{tier}/members', [TierController::class, 'members'])->name('tiers.members');
    

    // ទំព័រ Referral
    Route::get('/loyalty/referrals', [App\Http\Controllers\Frontend\LoyaltyController::class, 'referrals'])->name('loyalty.referrals');
    
    
    // ------------------ Loyalty Program Management (Dropdown Routes) ------------------
    Route::prefix('loyalty')->name('loyalty.')->group(function () {
        Route::get('/tiers', [AdminLoyaltyController::class, 'tiersIndex'])->name('tiers.index');
        Route::get('/rules', [AdminLoyaltyController::class, 'rulesIndex'])->name('rules.index');
        Route::get('/campaigns', [AdminLoyaltyController::class, 'campaignsIndex'])->name('campaigns.index');
        Route::get('/rewards', [AdminLoyaltyController::class, 'rewardsIndex'])->name('rewards.index');
        Route::get('/redemptions', [AdminLoyaltyController::class, 'redemptionsIndex'])->name('redemptions.index');
    });

    // System Settings
    Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::delete('settings/qr', [AdminSettingController::class, 'deleteQr'])->name('settings.delete-qr');
    
    // Referral & Marketing Sync
    Route::get('referral-programs/sync-old', [ReferralProgramController::class, 'syncOldReferrals'])->name('referral-programs.sync-old');

    // User Role Assignment
    Route::put('users/{id}/assign-role', [ReportController::class, 'assignRole'])->name('users.assign-role');

    // Route បណ្ដោះអាសន្នសម្រាប់គណនាពិន្ទុ និង Tier ឡើងវិញ
    Route::get('recalculate-loyalty', function() {
        $users = \App\Models\User::where('role', 'customer')->get();
        $updatedCount = 0;

        foreach($users as $user) {
            $lifetimeValue = \App\Models\Order::where('user_id', $user->id)->sum('total');
            
            if ($lifetimeValue > 0) {
                $calculatedPoints = round($lifetimeValue); 
                $matchedTier = \App\Models\Tier::where('min_points', '<=', $calculatedPoints)
                                           ->orderBy('level', 'desc')
                                           ->first();
                                           
                $user->update([
                    'points'          => $calculatedPoints, 
                    'lifetime_points' => $calculatedPoints,
                    'tier_id'         => $matchedTier ? $matchedTier->id : null
                ]);
                
                $updatedCount++;
            }
        }
        return "🎉 បានធ្វើបច្ចុប្បន្នភាពទិន្នន័យ Point & Tier ជូនអតិថិជនចាស់ៗចំនួន " . $updatedCount . " នាក់ជោគជ័យ!";
    })->name('reports.recalculate-loyalty');
});

// ------------------ Extends Sub-Routes Files ------------------
require __DIR__.'/admin.php';
require __DIR__.'/customer.php';

// ------------------ Developer Utility Routes ------------------
Route::get('/sync-past-points', function() {
    $orders = Order::where('status', 'delivered')
                   ->where(function($query) {
                       $query->where('points_awarded', false)
                             ->orWhereNull('points_awarded');
                   })
                   ->get();
                   
    $controller = app(AdminOrderController::class); 
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('awardLoyaltyPoints');
    $method->setAccessible(true);
    
    $count = 0;
    foreach($orders as $order) {
        $method->invokeArgs($controller, [$order]);
        $count++;
    }
    
    return $count . " orders have been successfully awarded loyalty points!";
});