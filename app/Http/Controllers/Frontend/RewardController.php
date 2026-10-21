<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RewardController extends Controller
{
    /**
     * បង្ហាញទំនិញរង្វាន់ទាំងអស់នៅលើ Frontend
     */
    public function index()
    {
        // ១. ទាញយករង្វាន់ណាដែលសកម្ម (is_active = 1) យោងតាមរូបភាព image_ab5547.jpg
        $rewards = DB::table('rewards')->where('is_active', 1)->get();
        
        // ២. ទាញយកប្រវត្តិដោះដូររបស់ Customer ម្នាក់នេះ យោងតាមរូបភាព image_ab556b.jpg
        $myRedemptions = DB::table('reward_redemptions')
            ->join('rewards', 'reward_redemptions.reward_id', '=', 'rewards.id')
            ->where('reward_redemptions.user_id', Auth::id())
            ->select('reward_redemptions.*', 'rewards.name as reward_name', 'rewards.image as reward_image')
            ->latest()
            ->get();

        return view('frontend.rewards.index', compact('rewards', 'myRedemptions'));
    }

    /**
     * មុខងារកាត់ Point និងបង្កើតការប្តូររង្វាន់ (Redeem)
     */
   public function redeem(Request $request, $id)
    {
        $user = Auth::user();
        $reward = \App\Models\Reward::find($id);

        // ពិនិត្យមើលថាតើ User มี point គ្រប់គ្រាន់ដែរឬទេ
        if ($user->points_balance < $reward->point_cost) {
            return back()->with('error', 'ពិន្ទុរបស់អ្នកមិនគ្រប់គ្រាន់ទេ!');
        }

        DB::transaction(function () use ($user, $reward) {
            
            // ១. កាត់ពិន្ទុ User
            $user->decrement('points_balance', $reward->point_cost);

            // ២. កាត់ស្តុក
            $reward->decrement('total_stock', 1);
            $reward->increment('redeemed_count', 1);

            // ៣. បង្កើតប្រវត្តិចូលទៅក្នុង Table reward_redemptions (កន្លែងនេះហើយដែលធ្វើឱ្យ Admin ឃើញ)
            \App\Models\RewardRedemption::create([
                'user_id'         => $user->id,
                'reward_id'       => $reward->id,
                'points_spent'    => $reward->point_cost,
                'redemption_code' => 'RDM-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'status'          => 'pending',
            ]);

            // ៤. បន្ថែមចូល points_transactions ផង (ដើម្បីឱ្យ Frontend របស់អ្នកបង្ហាញប្រវត្តិការកាត់ពិន្ទុ)
            \App\Models\PointsTransaction::create([
                'user_id'     => $user->id,
                'points'      => -$reward->point_cost,
                'type'        => 'redeem',
                'description' => 'Redeemed reward: ' . $reward->name,
            ]);
        });

        return back()->with('success', 'ការប្តូររង្វាន់បានជោគជ័យ!');
    }
}