<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tier;
use App\Models\PointsRule;
use App\Models\PointsCampaign;
use App\Models\Reward;
use App\Models\RewardRedemption;

class LoyaltyController extends Controller
{
    public function tiersIndex()
    {
        $tiers = Tier::orderBy('level')->get();
        return view('admin.tiers.index', compact('tiers'));
    }

    public function rulesIndex()
    {
        $rules = PointsRule::paginate(10); // ប្រើ paginate ដើម្បីគាំទ្រ links()
        return view('admin.points-rules.index', compact('rules'));
    }

    public function campaignsIndex()
    {
        $campaigns = PointsCampaign::paginate(10);
        return view('admin.points-campaigns.index', compact('campaigns'));
    }

    public function rewardsIndex()
    {
        $rewards = Reward::paginate(10);
        return view('admin.rewards.index', compact('rewards'));
    }

    public function redemptionsIndex()
    {
        $redemptions = RewardRedemption::paginate(10);
        return view('admin.redemptions.index', compact('redemptions'));
    }
}