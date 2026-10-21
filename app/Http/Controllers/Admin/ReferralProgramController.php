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

    /**
     * Route បណ្ដោះអាសន្នសម្រាប់ទាញទិន្នន័យអ្នកចុះឈ្មោះចាស់ៗដែលមាន referred_by ចូលតារាង referrals
     */
   public function syncOldReferrals()
    {
        // ១. ស្វែងរកកម្មវិធីរង្វាន់ដែលកំពុង Active
        $program = \App\Models\ReferralProgram::where('is_active', true)->first();
        if (!$program) {
            return "សូមបង្កើត ឬបើកដំណើរការ (Active) កម្មវិធី Referral យ៉ាងហោចណាស់មួយសិន!";
        }

        // ២. ទាញយក User ចំនួន ២ នាក់ដំបូងគេក្នុង Database មកធ្វើជាអ្នកតេស្ត
        $allUsers = \App\Models\User::limit(3)->get();
        
        if ($allUsers->count() < 2) {
            return "ប្រព័ន្ធត្រូវការ User យ៉ាងហោចណាស់ ២ នាក់នៅក្នុង Database ដើម្បីធ្វើការតេស្ត Referral!";
        }

        $referrer = $allUsers[0]; // ឱ្យ User ទី ១ ធ្វើជាអ្នកណែនាំ (Referrer)
        $referee = $allUsers[1];  // ឱ្យ User ទី ២ ធ្វើជាអ្នកត្រូវបានណែនាំ (Referee)

        // ៣. កំណត់តម្លៃក្នុងតារាង users ឱ្យស្គាល់គ្នា
        $referee->update(['referred_by' => $referrer->id]);

        // ៤. ពិនិត្យមើល និងបង្កើតទិន្នន័យចូលតារាង referrals
        $exists = \App\Models\Referral::where('referee_id', $referee->id)->exists();
        $syncedCount = 0;

        if (!$exists) {
            // សន្មតថាគាត់ធ្លាប់ទិញអីវ៉ាន់ជោគជ័យដើម្បីឱ្យវាលោត Converted
            \App\Models\Referral::create([
                'referral_program_id' => $program->id,
                'referrer_id'         => $referrer->id,
                'referee_id'          => $referee->id,
                'status'              => \App\Models\Referral::STATUS_CONVERTED, // ដាក់ជា Converted តែម្តង
                'referral_link_code'  => 'TEST-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(4)),
                'registered_at'       => now()->subDays(2),
                'converted_at'        => now(),
                'ip_address'          => request()->ip(),
            ]);
            
            $syncedCount++;
        }

        return "🎉 ប្រព័ន្ធបានបង្កើតទិន្នន័យតេស្ត Referral រវាង (User ID: {$referrer->id}) ណែនាំ (User ID: {$referee->id}) ចំនួន " . $syncedCount . " គូជោគជ័យ!";
    }
}
