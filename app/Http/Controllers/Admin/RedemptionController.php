<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardRedemption;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class RedemptionController extends Controller
{
    public function __construct(protected ActivityLogService $activityLog) {}

    /** View Redemption History (Module #7) */
    public function index(Request $request)
    {
        $query = RewardRedemption::with(['user', 'reward'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $redemptions = $query->paginate(20)->withQueryString();
        return view('admin.redemptions.index', compact('redemptions'));
    }

    public function show(RewardRedemption $redemption)
    {
        return view('admin.redemptions.show', compact('redemption'));
    }

    /** Process Redemption — mark as fulfilled */
    public function fulfill(RewardRedemption $redemption)
    {
        if ($redemption->status !== RewardRedemption::STATUS_PENDING) {
            return back()->with('error', 'Only pending redemptions can be fulfilled.');
        }

        $redemption->update([
            'status'       => RewardRedemption::STATUS_FULFILLED,
            'fulfilled_at' => now(),
        ]);

        $this->activityLog->log('redemption.fulfilled', $redemption);

        return back()->with('success', 'Redemption marked as fulfilled.');
    }

    public function cancel(Request $request, RewardRedemption $redemption)
    {
        $request->validate(['reason' => 'required|string|max:255']);

        if ($redemption->status !== RewardRedemption::STATUS_PENDING) {
            return back()->with('error', 'Only pending redemptions can be cancelled.');
        }

        $redemption->update(['status' => RewardRedemption::STATUS_CANCELLED]);

        // Refund the points spent
        $redemption->user->increment('points_balance', $redemption->points_spent);

        $this->activityLog->log('redemption.cancelled', $redemption, [
            'metadata' => ['reason' => $request->reason, 'points_refunded' => $redemption->points_spent],
        ]);

        return back()->with('success', 'Redemption cancelled and points refunded.');
    }
}
