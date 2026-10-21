<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{PointsCampaign, Category, Product, Tier};
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class PointsCampaignController extends Controller
{
    public function __construct(protected ActivityLogService $activityLog) {}

    public function index()
    {
        $campaigns = PointsCampaign::with(['category', 'product', 'tier'])->latest()->paginate(15);
        return view('admin.points-campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $categories = Category::all();
        $products   = Product::all();
        $tiers      = Tier::active()->get();
        return view('admin.points-campaigns.create', compact('categories', 'products', 'tiers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:150',
            'description'  => 'nullable|string|max:255',
            'multiplier'   => 'required|numeric|min:1.1',
            'category_id'  => 'nullable|exists:categories,id',
            'product_id'   => 'nullable|exists:products,id',
            'tier_id'      => 'nullable|exists:tiers,id',
            'starts_at'    => 'required|date',
            'ends_at'      => 'required|date|after:starts_at',
        ]);

        $validated['created_by'] = auth()->id();
        $campaign = PointsCampaign::create($validated);

        $this->activityLog->log('points_campaign.created', $campaign, ['new_values' => $validated]);

        return redirect()->route('admin.points-campaigns.index')->with('success', 'Bonus campaign launched.');
    }

    public function edit(PointsCampaign $pointsCampaign)
    {
        $categories = Category::all();
        $products   = Product::all();
        $tiers      = Tier::active()->get();
        return view('admin.points-campaigns.edit', compact('pointsCampaign', 'categories', 'products', 'tiers'));
    }

    public function update(Request $request, PointsCampaign $pointsCampaign)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'multiplier'  => 'required|numeric|min:1.1',
            'starts_at'   => 'required|date',
            'ends_at'     => 'required|date|after:starts_at',
            'is_active'   => 'boolean',
        ]);

        $old = $pointsCampaign->toArray();
        $pointsCampaign->update($validated);

        $this->activityLog->log('points_campaign.updated', $pointsCampaign, [
            'old_values' => $old, 'new_values' => $validated,
        ]);

        return redirect()->route('admin.points-campaigns.index')->with('success', 'Campaign updated.');
    }

    public function destroy(PointsCampaign $pointsCampaign)
    {
        $this->activityLog->log('points_campaign.deleted', $pointsCampaign, ['old_values' => $pointsCampaign->toArray()]);
        $pointsCampaign->delete();

        return redirect()->route('admin.points-campaigns.index')->with('success', 'Campaign deleted.');
    }

    public function toggleActive(PointsCampaign $pointsCampaign)
    {
        $pointsCampaign->update(['is_active' => !$pointsCampaign->is_active]);
        $this->activityLog->log('points_campaign.toggled', $pointsCampaign, [
            'metadata' => ['is_active' => $pointsCampaign->is_active],
        ]);

        return back()->with('success', 'Campaign status toggled.');
    }
}
