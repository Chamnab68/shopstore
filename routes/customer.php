<?php

use App\Http\Controllers\Frontend\{LoyaltyController, RewardCatalogController, ReferralController};
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\RewardController;

/*
|--------------------------------------------------------------------------
| Customer-Facing Loyalty Routes
| All routes require auth. Named "loyalty.*"
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('loyalty')->name('loyalty.')->group(function () {

    // ── Points & Tier Dashboard ───────────────────────────────────────────────
    Route::get('/', [LoyaltyController::class, 'dashboard'])->name('dashboard');
    Route::get('/points/history', [LoyaltyController::class, 'pointsHistory'])->name('points.history');
    Route::get('/tiers', [LoyaltyController::class, 'tierBenefits'])->name('tiers.benefits');
    Route::get('/tiers/history', [LoyaltyController::class, 'tierHistory'])->name('tiers.history');

    // ── Rewards Catalog & Redemption ──────────────────────────────────────────
    Route::get('/rewards', [RewardCatalogController::class, 'index'])->name('rewards.index');
    Route::get('/rewards/{reward}', [RewardCatalogController::class, 'show'])->name('rewards.show');
    Route::post('/rewards/{reward}/redeem', [RewardCatalogController::class, 'redeem'])->name('rewards.redeem');
    Route::get('/redemptions', [RewardCatalogController::class, 'myRedemptions'])->name('redemptions.index');
    Route::get('/redemptions/{redemption}', [RewardCatalogController::class, 'showRedemption'])->name('redemptions.show');

    // ── Referral Program ──────────────────────────────────────────────────────
    Route::get('/referrals', [ReferralController::class, 'dashboard'])->name('referrals.dashboard');
    Route::get('/referrals/list', [ReferralController::class, 'myReferrals'])->name('referrals.list');
    Route::post('/referrals/generate-link', [ReferralController::class, 'generateNewLink'])->name('referrals.generate-link');

    Route::middleware(['auth'])->group(function () {
    // ទំព័របង្ហាញទំនិញរង្វាន់ទាំងអស់
    Route::get('/rewards', [RewardController::class, 'index'])->name('rewards.index');
    
    // មុខងារសម្រាប់ចុចប្តូររង្វាន់
    Route::post('/rewards/{id}/redeem', [RewardController::class, 'redeem'])->name('rewards.redeem');
});

});
