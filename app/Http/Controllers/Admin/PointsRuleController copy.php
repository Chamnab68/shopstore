<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{PointsRule, User};
use App\Services\{PointsEngineService, ActivityLogService};
use Illuminate\Http\Request;

class PointsRuleController extends Controller
{
    public function __construct(
        protected PointsEngineService $pointsEngine,
        protected ActivityLogService $activityLog
    ) {}

    public function index()
    {
        $rules = PointsRule::orderByDesc('priority')->paginate(15);
        return view('admin.points-rules.index', compact('rules'));
    }

    public function create()
    {
        return view('admin.points-rules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'type'              => 'required|in:earn_rate,weekend_bonus,category_bonus,min_order',
            'points_per_dollar' => 'required|numeric|min:0',
            'multiplier'        => 'required|numeric|min:0.1',
            'category_id'       => 'nullable|exists:categories,id',
            'min_order_amount'  => 'nullable|numeric|min:0',
            'active_days'       => 'nullable|array',
            'starts_at'         => 'nullable|date',
            'ends_at'           => 'nullable|date|after_or_equal:starts_at',
            'priority'          => 'required|integer|min:0',
        ]);

        $rule = PointsRule::create($validated);
        $this->activityLog->log('points_rule.created', $rule, ['new_values' => $validated]);

        return redirect()->route('admin.points-rules.index')->with('success', 'Points rule created.');
    }

    public function edit(PointsRule $pointsRule)
    {
        return view('admin.points-rules.edit', compact('pointsRule'));
    }

    public function update(Request $request, PointsRule $pointsRule)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'points_per_dollar' => 'required|numeric|min:0',
            'multiplier'        => 'required|numeric|min:0.1',
            'min_order_amount'  => 'nullable|numeric|min:0',
            'active_days'       => 'nullable|array',
            'starts_at'         => 'nullable|date',
            'ends_at'           => 'nullable|date|after_or_equal:starts_at',
            'is_active'         => 'boolean',
            'priority'          => 'required|integer|min:0',
        ]);

        $old = $pointsRule->toArray();
        $pointsRule->update($validated);

        $this->activityLog->log('points_rule.updated', $pointsRule, [
            'old_values' => $old, 'new_values' => $validated,
        ]);

        return redirect()->route('admin.points-rules.index')->with('success', 'Points rule updated.');
    }

    public function destroy(PointsRule $pointsRule)
    {
        $this->activityLog->log('points_rule.deleted', $pointsRule, ['old_values' => $pointsRule->toArray()]);
        $pointsRule->delete();

        return redirect()->route('admin.points-rules.index')->with('success', 'Points rule deleted.');
    }

    // ── Set Points Expiry Policy (stored as a special rule type or config) ──
    public function expiryPolicy()
    {
        return view('admin.points-rules.expiry-policy');
    }

    public function updateExpiryPolicy(Request $request)
    {
        $validated = $request->validate(['expiry_months' => 'required|integer|min:1|max:60']);

        // Persisted via a settings table/config (implementation depends on your Setting model)
        \App\Models\Setting::updateOrCreate(
            ['key' => 'points_expiry_months'],
            ['value' => $validated['expiry_months']]
        );

        $this->activityLog->log('points_policy.expiry_updated', null, ['new_values' => $validated]);

        return back()->with('success', 'Points expiry policy updated.');
    }

    // ── Admin manual points adjustment (mandatory reason log) ───────────────
    public function adjustForm(User $user)
    {
        return view('admin.points-rules.adjust', compact('user'));
    }

    public function adjust(Request $request, User $user)
    {
        $validated = $request->validate([
            'points' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:255',
        ]);

        $this->pointsEngine->manualAdjust($user, $validated['points'], $validated['reason'], auth()->id());

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'Points balance manually adjusted.');
    }
}
