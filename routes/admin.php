<?php

use App\Http\Controllers\Admin\{
    RoleController, PermissionController, ActivityLogController,
    TierController, PointsRuleController, PointsCampaignController,
    RewardController, RedemptionController, BadgeController,
    ReferralProgramController, AnalyticsController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes — Loyalty & Reward-Driven E-Commerce Platform
| All routes are prefixed "admin/" and named "admin.*"
| Wrap with middleware(['auth', 'permission:<name>']) per route as needed
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // ── Module #1: Role & Permission Management ─────────────────────────────
    Route::middleware('permission:roles.manage')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::post('roles/{role}/permissions/assign', [RoleController::class, 'assignPermission'])->name('roles.permissions.assign');
        Route::delete('roles/{role}/permissions/revoke', [RoleController::class, 'revokePermission'])->name('roles.permissions.revoke');
    });
    Route::middleware('permission:permissions.manage')->group(function () {
        Route::resource('permissions', PermissionController::class);
    });

    // ── Module #2: Activity Log (Mandatory) ──────────────────────────────────
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
    Route::get('activity-logs/trail', [ActivityLogController::class, 'trailFor'])->name('activity-logs.trail');
    Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');

    // ── Module #5: Loyalty Points Engine Management ──────────────────────────
    Route::middleware('permission:points.manage')->group(function () {
        Route::resource('points-rules', PointsRuleController::class)->except(['show']);
        Route::get('points-rules/expiry-policy', [PointsRuleController::class, 'expiryPolicy'])->name('points-rules.expiry-policy');
        Route::post('points-rules/expiry-policy', [PointsRuleController::class, 'updateExpiryPolicy'])->name('points-rules.expiry-policy.update');
        Route::resource('points-campaigns', PointsCampaignController::class)->except(['show']);
        Route::patch('points-campaigns/{pointsCampaign}/toggle', [PointsCampaignController::class, 'toggleActive'])->name('points-campaigns.toggle');
        Route::get('users/{user}/points/adjust', [PointsRuleController::class, 'adjustForm'])->name('users.points.adjust-form');
        Route::post('users/{user}/points/adjust', [PointsRuleController::class, 'adjust'])->name('users.points.adjust');
    });

    // ── Module #6: Tier & Membership Management ───────────────────────────────
    Route::middleware('permission:tiers.manage')->group(function () {
        Route::resource('tiers', TierController::class)->except(['show']);
        Route::post('tiers/{tier}/benefits', [TierController::class, 'addBenefit'])->name('tiers.benefits.add');
        Route::delete('tiers/{tier}/benefits/{benefit}', [TierController::class, 'removeBenefit'])->name('tiers.benefits.remove');
        Route::post('tiers/run-quarterly-review', [TierController::class, 'runQuarterlyReview'])->name('tiers.quarterly-review');
        Route::get('users/{user}/tier/override', [TierController::class, 'overrideForm'])->name('users.tier.override-form');
        Route::post('users/{user}/tier/override', [TierController::class, 'override'])->name('users.tier.override');
        Route::get('users/{user}/tier/history', [TierController::class, 'history'])->name('users.tier.history');
    });

    // ── Module #7: Rewards & Redemption Management ────────────────────────────
    Route::middleware('permission:rewards.manage')->group(function () {
        Route::resource('rewards', RewardController::class)->except(['show']);
        Route::get('rewards-catalog', [RewardController::class, 'catalog'])->name('rewards.catalog');
        Route::get('redemptions', [RedemptionController::class, 'index'])->name('redemptions.index');
        Route::get('redemptions/{redemption}', [RedemptionController::class, 'show'])->name('redemptions.show');
        Route::patch('redemptions/{redemption}/fulfill', [RedemptionController::class, 'fulfill'])->name('redemptions.fulfill');
        Route::patch('redemptions/{redemption}/cancel', [RedemptionController::class, 'cancel'])->name('redemptions.cancel');
    });

    // ── Module #8: Gamification & Badge Management ────────────────────────────
    Route::middleware('permission:badges.manage')->group(function () {
        Route::resource('badges', BadgeController::class)->except(['show']);
        Route::get('badges-gallery', [BadgeController::class, 'gallery'])->name('badges.gallery');
        Route::get('users/{user}/badges/award', [BadgeController::class, 'awardForm'])->name('users.badges.award-form');
        Route::post('users/{user}/badges/award', [BadgeController::class, 'award'])->name('users.badges.award');
    });

    // ── Module #9: Referral & Affiliate Program Management ────────────────────
    Route::middleware('permission:referrals.manage')->group(function () {
        Route::resource('referral-programs', ReferralProgramController::class)->except(['show']);
        Route::get('referral-programs-stats', [ReferralProgramController::class, 'stats'])->name('referral-programs.stats');
        Route::get('referral-conversions', [ReferralProgramController::class, 'conversions'])->name('referral-programs.conversions');
        Route::get('referral-fraud', [ReferralProgramController::class, 'fraudList'])->name('referral-programs.fraud');
        Route::patch('referral-fraud/{referral}/confirm', [ReferralProgramController::class, 'markFraudReviewed'])->name('referral-programs.fraud.confirm');
        Route::patch('referral-commissions/{commission}/payout', [ReferralProgramController::class, 'processPayout'])->name('referral-commissions.payout');
    });

    // ── Module #10: Customer Behavior & Retention Analytics ───────────────────
    Route::middleware('permission:analytics.view')->group(function () {
        Route::get('analytics/points-liability', [AnalyticsController::class, 'pointsLiability'])->name('analytics.points-liability');
        Route::get('analytics/tier-distribution', [AnalyticsController::class, 'tierDistribution'])->name('analytics.tier-distribution');
        Route::get('analytics/redemption-rate', [AnalyticsController::class, 'redemptionRate'])->name('analytics.redemption-rate');
        Route::get('analytics/churn-prediction', [AnalyticsController::class, 'churnPrediction'])->name('analytics.churn-prediction');
        Route::get('analytics/campaign-roi/{campaign}', [AnalyticsController::class, 'campaignRoi'])->name('analytics.campaign-roi');
        Route::get('analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
    });

});
