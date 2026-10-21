<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ReferralProgram, Referral, ReferralCommission};
use App\Services\{ReferralService, ActivityLogService};
use Illuminate\Http\Request;

class ReferralProgramController extends Controller
{
    public function __construct(
        protected ReferralService $referralService,
        protected ActivityLogService $activityLog
    ) {}

    public function index()
    {
        $programs = ReferralProgram::withCount('referrals')->latest()->paginate(15);
        $stats    = $this->referralService->getStats();
        return view('admin.referral-programs.index', compact('programs', 'stats'));
    }

    public function create()
    {
        return view('admin.referral-programs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                       => 'required|string|max:150',
            'referrer_reward_points'     => 'required|integer|min:0',
            'referee_reward_points'      => 'required|integer|min:0',
            'commission_percent'         => 'nullable|numeric|min:0|max:100',
            'max_levels'                 => 'required|integer|min:1|max:3',
            'level2_commission_percent'  => 'nullable|numeric|min:0|max:100',
            'requires_first_purchase'    => 'boolean',
        ]);

        $program = ReferralProgram::create($validated);
        $this->activityLog->log('referral_program.created', $program, ['new_values' => $validated]);

        return redirect()->route('admin.referral-programs.index')->with('success', 'Referral program created.');
    }

    public function edit(ReferralProgram $referralProgram)
    {
        return view('admin.referral-programs.edit', compact('referralProgram'));
    }

    /** Update Commission Rules */
    public function update(Request $request, ReferralProgram $referralProgram)
    {
        $validated = $request->validate([
            'referrer_reward_points'    => 'required|integer|min:0',
            'referee_reward_points'     => 'required|integer|min:0',
            'commission_percent'        => 'nullable|numeric|min:0|max:100',
            'level2_commission_percent' => 'nullable|numeric|min:0|max:100',
            'is_active'                 => 'boolean',
        ]);

        $old = $referralProgram->toArray();
        $referralProgram->update($validated);

        $this->activityLog->log('referral_program.updated', $referralProgram, [
            'old_values' => $old, 'new_values' => $validated,
        ]);

        return redirect()->route('admin.referral-programs.index')->with('success', 'Commission rules updated.');
    }

    public function destroy(ReferralProgram $referralProgram)
    {
        $this->activityLog->log('referral_program.deleted', $referralProgram, ['old_values' => $referralProgram->toArray()]);
        $referralProgram->delete();

        return redirect()->route('admin.referral-programs.index')->with('success', 'Program deleted.');
    }

    /** View Referral Stats (Module #9) */
    public function stats()
    {
        $stats = $this->referralService->getStats();
        $topReferrers = Referral::selectRaw('referrer_id, COUNT(*) as total')
            ->where('status', Referral::STATUS_CONVERTED)
            ->groupBy('referrer_id')
            ->orderByDesc('total')
            ->with('referrer')
            ->limit(10)
            ->get();

        return view('admin.referral-programs.stats', compact('stats', 'topReferrers'));
    }

    /** Track Conversions list */
    public function conversions()
    {
        $referrals = Referral::with(['referrer', 'referee', 'program'])
            ->where('status', Referral::STATUS_CONVERTED)
            ->latest('converted_at')
            ->paginate(20);

        return view('admin.referral-programs.conversions', compact('referrals'));
    }

    /** Detect Fraud — flagged referrals list */
    public function fraudList()
    {
        $flagged = Referral::with(['referrer', 'referee'])
            ->where('is_flagged_fraud', true)
            ->latest()
            ->paginate(20);

        return view('admin.referral-programs.fraud', compact('flagged'));
    }

    public function markFraudReviewed(Referral $referral)
    {
        $referral->update(['status' => Referral::STATUS_REJECTED_FRAUD]);
        $this->activityLog->log('referral.fraud_reviewed', $referral);

        return back()->with('success', 'Referral marked as confirmed fraud.');
    }

    /** Process Payout for pending commissions */
    public function processPayout(ReferralCommission $commission)
    {
        $commission->update([
            'payout_status' => ReferralCommission::PAYOUT_PAID,
            'paid_at'       => now(),
        ]);

        $this->activityLog->log('referral_commission.paid', $commission);

        return back()->with('success', 'Commission payout processed.');
    }
}
