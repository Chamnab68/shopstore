<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\{Reward, RewardRedemption};
use App\Services\{PointsEngineService, BadgeService, ActivityLogService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};

class RewardCatalogController extends Controller
{
    public function __construct(
        protected PointsEngineService $pointsEngine,
        protected ActivityLogService $activityLog
    ) {}

    /** Browse reward catalog — only show what the user's tier/points can access */
    public function index()
    {
        $user = Auth::user();

        $rewards = Reward::active()
            ->with('minTier')
            ->when($user->tier_id, function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->whereNull('min_tier_id')->orWhere('min_tier_id', '<=', $user->tier_id);
                });
            }, function ($q) {
                $q->whereNull('min_tier_id');
            })
            ->paginate(12);

        return view('frontend.rewards.index', compact('rewards'));
    }

    public function show(Reward $reward)
    {
        $user = Auth::user();
        $canRedeem = $reward->isAvailableForUser($user);

        return view('frontend.rewards.show', compact('reward', 'canRedeem'));
    }

    /** Redeem a reward — burns points and creates a redemption record */
    public function redeem(Request $request, $id)
    {
        $user = Auth::user();
        $reward = \App\Models\Reward::find($id);

        // ពិនិត្យលក្ខខណ្ឌផ្សេងៗ (ពិន្ទុគ្រប់, ស្តុកមាន...)
        if (!$reward || $user->points_balance < $reward->point_cost) {
            return back()->with('error', 'ពិន្ទុមិនគ្រប់គ្រាន់ ឬរង្វាន់មិនមានទេ។');
        }

        DB::transaction(function () use ($user, $reward) {
            // ១. កាត់ពិន្ទុ User
            $user->decrement('points_balance', $reward->point_cost);

            // ២. កាត់ស្តុក
            $reward->decrement('total_stock', 1);
            $reward->increment('redeemed_count', 1);

            // 📍 3. យកកូដនេះមកដាក់ជំនួសកន្លែង Insert  cũ របស់អ្នកត្រង់នេះ 👇
            \App\Models\RewardRedemption::create([
                'user_id'         => $user->id,
                'reward_id'       => $reward->id,
                'points_spent'    => $reward->point_cost,
                'redemption_code' => 'RDM-' . strtoupper(Str::random(8)),
                'status'          => 'pending',
            ]);
        });

        return back()->with('success', 'ការប្តូររង្វាន់បានជោគជ័យ!');
    }

    /** My redemption history */
    public function myRedemptions()
    {
        $redemptions = Auth::user()->rewardRedemptions()->with('reward')->latest()->paginate(15);
        return view('frontend.rewards.my-redemptions', compact('redemptions'));
    }

    public function showRedemption(RewardRedemption $redemption)
    {
        abort_if($redemption->user_id !== Auth::id(), 403);
        return view('frontend.rewards.redemption-detail', compact('redemption'));
    }
}
