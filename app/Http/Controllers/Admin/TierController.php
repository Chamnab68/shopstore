<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Tier, TierBenefit, TierHistory, User};
use App\Services\{TierService, ActivityLogService};
use Illuminate\Http\Request;

class TierController extends Controller
{
    public function __construct(
        protected TierService $tierService,
        protected ActivityLogService $activityLog
    ) {}

    public function index()
    {
        $tiers = Tier::withCount('users')->orderBy('level')->get();
        return view('admin.tiers.index', compact('tiers'));
    }

    public function create()
    {
        return view('admin.tiers.create');
    }

    public function members(Tier $tier)
{
    // ទាញយក Users ដែលមាន Tier នេះ (ផ្អែកលើ relation users ឬ tier_id)
    $users = $tier->users()->paginate(10); 
    
    return view('admin.tiers.members', compact('tier', 'users'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:50',
            'level'                 => 'required|integer|unique:tiers,level',
            'min_spend'             => 'required|integer|min:0',
            'min_points'            => 'required|integer|min:0',
            'point_earn_multiplier' => 'required|numeric|min:1',
            'free_shipping'         => 'boolean',
            'priority_support'      => 'boolean',
            'exclusive_access'      => 'boolean',
            'badge_color'           => 'nullable|string|max:20',
        ]);

        $tier = Tier::create($validated);
        $this->activityLog->log('tier.created', $tier, ['new_values' => $validated]);

        return redirect()->route('admin.tiers.index')->with('success', 'Tier created.');
    }

    public function edit(Tier $tier)
    {
        $benefits = $tier->benefits;
        return view('admin.tiers.edit', compact('tier', 'benefits'));
    }

    public function update(Request $request, Tier $tier)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:50',
            'min_spend'             => 'required|integer|min:0',
            'min_points'            => 'required|integer|min:0',
            'point_earn_multiplier' => 'required|numeric|min:1',
            'free_shipping'         => 'boolean',
            'priority_support'      => 'boolean',
            'exclusive_access'      => 'boolean',
            'badge_color'           => 'nullable|string|max:20',
            'is_active'             => 'boolean',
        ]);

        $old = $tier->toArray();
        $tier->update($validated);

        $this->activityLog->log('tier.updated', $tier, ['old_values' => $old, 'new_values' => $validated]);

        return redirect()->route('admin.tiers.index')->with('success', 'Tier updated.');
    }

    public function destroy(Tier $tier)
    {
        if ($tier->users()->exists()) {
            return back()->with('error', 'Cannot delete a tier with active members.');
        }

        $this->activityLog->log('tier.deleted', $tier, ['old_values' => $tier->toArray()]);
        $tier->delete();

        return redirect()->route('admin.tiers.index')->with('success', 'Tier deleted.');
    }

    // ── Tier Benefits CRUD ──────────────────────────────────────────────────
    public function addBenefit(Request $request, Tier $tier)
    {
        $validated = $request->validate([
            'benefit_key'   => 'required|string|max:50',
            'benefit_value' => 'required|string|max:255',
            'description'   => 'nullable|string|max:255',
        ]);

        $tier->benefits()->create($validated);
        $this->activityLog->log('tier_benefit.created', $tier, ['new_values' => $validated]);

        return back()->with('success', 'Benefit added.');
    }

    public function removeBenefit(Tier $tier, TierBenefit $benefit)
    {
        $benefit->delete();
        $this->activityLog->log('tier_benefit.deleted', $tier);

        return back()->with('success', 'Benefit removed.');
    }

    // ── Manual Override ──────────────────────────────────────────────────────
    public function overrideForm(User $user)
    {
        $tiers = Tier::active()->orderBy('level')->get();
        return view('admin.tiers.override', compact('user', 'tiers'));
    }

    public function override(Request $request, User $user)
    {
        $validated = $request->validate([
            'tier_id' => 'required|exists:tiers,id',
            'reason'  => 'required|string|max:255',
        ]);

        $newTier = Tier::findOrFail($validated['tier_id']);
        $this->tierService->manualOverride($user, $newTier, $validated['reason'], auth()->id());

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', "User tier manually overridden to {$newTier->name}.");
    }

    // ── Scheduled Quarterly Review (manual trigger button) ───────────────────
    public function runQuarterlyReview()
    {
        $count = $this->tierService->runQuarterlyReview();
        return back()->with('success', "Quarterly tier review completed for {$count} members.");
    }

    public function history(User $user)
    {
        $history = TierHistory::with(['fromTier', 'toTier', 'changedBy'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('admin.tiers.history', compact('user', 'history'));
    }
}
