<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Badge, User};
use App\Services\{BadgeService, ActivityLogService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BadgeController extends Controller
{
    public function __construct(
        protected BadgeService $badgeService,
        protected ActivityLogService $activityLog
    ) {}

    public function index()
    {
        $badges = Badge::withCount('userBadges')->latest()->paginate(15);
        return view('admin.badges.index', compact('badges'));
    }

    public function create()
    {
        return view('admin.badges.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'icon'          => 'nullable|image|max:1024',
            'description'   => 'nullable|string|max:255',
            'trigger_type'  => 'required|in:first_purchase,order_milestone,referral_milestone,login_streak,points_milestone,manual',
            'trigger_value' => 'nullable|integer|min:1',
        ]);

        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon')->store('badges', 'public');
        }

        $badge = Badge::create($validated);
        $this->activityLog->log('badge.created', $badge, ['new_values' => $validated]);

        return redirect()->route('admin.badges.index')->with('success', 'Badge created.');
    }

    public function edit(Badge $badge)
    {
        return view('admin.badges.edit', compact('badge'));
    }

    public function update(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'icon'          => 'nullable|image|max:1024',
            'description'   => 'nullable|string|max:255',
            'trigger_value' => 'nullable|integer|min:1',
            'is_active'     => 'boolean',
        ]);

        if ($request->hasFile('icon')) {
            if ($badge->icon) {
                Storage::disk('public')->delete($badge->icon);
            }
            $validated['icon'] = $request->file('icon')->store('badges', 'public');
        }

        $old = $badge->toArray();
        $badge->update($validated);

        $this->activityLog->log('badge.updated', $badge, ['old_values' => $old, 'new_values' => $validated]);

        return redirect()->route('admin.badges.index')->with('success', 'Badge updated.');
    }

    public function destroy(Badge $badge)
    {
        if ($badge->icon) {
            Storage::disk('public')->delete($badge->icon);
        }

        $this->activityLog->log('badge.deleted', $badge, ['old_values' => $badge->toArray()]);
        $badge->delete();

        return redirect()->route('admin.badges.index')->with('success', 'Badge deleted.');
    }

    /** View Badge Gallery (Module #8) */
    public function gallery()
    {
        $badges = Badge::active()->withCount('userBadges')->get();
        return view('admin.badges.gallery', compact('badges'));
    }

    /** Manual Award Badge */
    public function awardForm(User $user)
    {
        $badges = Badge::active()->get();
        return view('admin.badges.award', compact('user', 'badges'));
    }

    public function award(Request $request, User $user)
    {
        $request->validate(['badge_id' => 'required|exists:badges,id']);
        $badge = Badge::findOrFail($request->badge_id);

        $awarded = $this->badgeService->manualAward($user, $badge, auth()->id());

        if (!$awarded) {
            return back()->with('error', 'User already has this badge.');
        }

        return redirect()->route('admin.users.show', $user->id)->with('success', 'Badge awarded.');
    }
}
