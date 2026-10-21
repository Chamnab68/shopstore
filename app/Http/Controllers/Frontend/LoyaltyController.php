<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\{PointsTransaction, Tier, TierHistory, Referral};
use Illuminate\Support\Facades\Auth;

class LoyaltyController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $currentTier = $user->tier;

        $nextTier = Tier::active()
            ->where('level', '>', $currentTier->level ?? 0)
            ->orderBy('level')
            ->first();

        $progress = null;
        if ($nextTier) {
            $spendProgress  = $nextTier->min_spend > 0
                ? min(100, round(($this->getLifetimeSpend($user) / $nextTier->min_spend) * 100))
                : 100;
            $pointsProgress = $nextTier->min_points > 0
                ? min(100, round(($user->lifetime_points / $nextTier->min_points) * 100))
                : 100;
            $progress = min($spendProgress, $pointsProgress);
        }

        $recentTransactions = $user->pointsTransactions()->latest()->limit(5)->get();

        return view('frontend.loyalty.dashboard', compact('user', 'currentTier', 'nextTier', 'progress', 'recentTransactions'));
    }

    public function pointsHistory()
    {
        $transactions = Auth::user()->pointsTransactions()
            ->with(['order', 'pointsRule', 'rewardRedemption.reward'])
            ->latest()
            ->paginate(20);

        return view('frontend.loyalty.points-history', compact('transactions'));
    }

    public function tierBenefits()
    {
        $tiers = Tier::active()->with('benefits')->orderBy('level')->get();
        $currentTier = Auth::user()->tier;

        return view('frontend.loyalty.tier-benefits', compact('tiers', 'currentTier'));
    }

    public function tierHistory()
    {
        $history = TierHistory::with(['fromTier', 'toTier'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('frontend.loyalty.tier-history', compact('history'));
    }

    protected function getLifetimeSpend($user): float
    {
        return $user->orders()->where('status', 'delivered')->sum('total');
    }

    /** គណនាទឹកប្រាក់ Available Referral Balance សរុបពី Database ចំៗ១០០% */
    /** គណនាទឹកប្រាក់ Available Referral Balance សរុបពី Database ចំៗ១០០% */
    public function referrals()
    {
        $user = Auth::user();

        // ១. រាប់ចំនួនមិត្តភក្តិដែលបានចុះឈ្មោះដោយយោងតាម column referred_by ក្នុងតារាង users ផ្ទាល់
        $totalReferred = \App\Models\User::where('referred_by', $user->id)->count();

        // ២. ទាញយកទឹកប្រាក់ Available Referral Balance ជា Dollar
        $referralBalance = $user->referral_cash_balance ?? 0.00;

        // ៣. បង្កើត Link សម្រាប់ Share
        $referralLink = url('/register?ref=' . $user->referral_code);

        return view('frontend.referrals.dashboard', compact('totalReferred', 'referralBalance', 'referralLink'));
    }
}