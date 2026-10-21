<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Reward, Product, Tier};
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RewardController extends Controller
{
    public function __construct(protected ActivityLogService $activityLog) {}

    public function index()
    {
        $rewards = Reward::with('minTier')->withCount('redemptions')->latest()->paginate(15);
        return view('admin.rewards.index', compact('rewards'));
    }

    public function create()
    {
        $products = Product::all();
        $tiers    = Tier::active()->orderBy('level')->get();
        return view('admin.rewards.create', compact('products', 'tiers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:150',
            'description'      => 'nullable|string',
            'image'            => 'nullable|image|max:2048',
            'type'             => 'required|in:voucher,free_product,experience,discount',
            'product_id'       => 'nullable|exists:products,id',
            'point_cost'       => 'required|integer|min:1',
            'min_tier_id'      => 'nullable|exists:tiers,id',
            'total_stock'      => 'nullable|integer|min:1',
            'per_member_limit' => 'nullable|integer|min:1',
            'valid_from'       => 'nullable|date',
            'valid_until'      => 'nullable|date|after:valid_from',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('rewards', 'public');
        }

        $reward = Reward::create($validated);
        $this->activityLog->log('reward.created', $reward, ['new_values' => $validated]);

        return redirect()->route('admin.rewards.index')->with('success', 'Reward created.');
    }

    public function edit(Reward $reward)
    {
        $products = Product::all();
        $tiers    = Tier::active()->orderBy('level')->get();
        return view('admin.rewards.edit', compact('reward', 'products', 'tiers'));
    }

    public function update(Request $request, Reward $reward)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:150',
            'description'      => 'nullable|string',
            'image'            => 'nullable|image|max:2048',
            'point_cost'       => 'required|integer|min:1',
            'min_tier_id'      => 'nullable|exists:tiers,id',
            'total_stock'      => 'nullable|integer|min:1',
            'per_member_limit' => 'nullable|integer|min:1',
            'valid_until'      => 'nullable|date',
            'is_active'        => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($reward->image) {
                Storage::disk('public')->delete($reward->image);
            }
            $validated['image'] = $request->file('image')->store('rewards', 'public');
        }

        $old = $reward->toArray();
        $reward->update($validated);

        $this->activityLog->log('reward.updated', $reward, ['old_values' => $old, 'new_values' => $validated]);

        return redirect()->route('admin.rewards.index')->with('success', 'Reward updated.');
    }

    public function destroy(Reward $reward)
    {
        if ($reward->image) {
            Storage::disk('public')->delete($reward->image);
        }

        $this->activityLog->log('reward.deleted', $reward, ['old_values' => $reward->toArray()]);
        $reward->delete();

        return redirect()->route('admin.rewards.index')->with('success', 'Reward deleted.');
    }

    /** Reward Catalog gallery view (Module #7: "View Reward Catalog") */
    public function catalog()
    {
        $rewards = Reward::active()->with('minTier')->paginate(20);
        return view('admin.rewards.catalog', compact('rewards'));
    }
}
