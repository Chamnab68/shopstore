<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoyaltyActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function loyaltyAnalytics()
    {
        // ១. គណនា Total Points Liability (ចំនួនពិន្ទុសរុបដែលកំពុងមានក្នុងដៃ User ទាំងអស់)
        $totalLiability = User::sum('loyalty_points');

        // ២. ការបែងចែក Tier របស់អតិថិជន (Tier Distribution)
        $tierDistribution = User::select('current_tier', DB::raw('count(*) as total'))
            ->groupBy('current_tier')
            ->get();

        // ៣. ស្វែងរក Churn Candidates (User មិនទាន់ទិញទំនិញលើសពី ៣០ ថ្ងៃ និងមានពិន្ទុលើសពី ១០០)
        $churnCandidates = User::where('updated_at', '<', now()->subDays(30))
            ->where('loyalty_points', '>', 100)
            ->get();

        // ៤. គណនា Churn Rate ជាភាគរយ (ប្រៀបធៀប Churn Candidates ជាមួយចំនួន User សរុប)
        $totalUsers = User::count();
        $churnRate = $totalUsers > 0 ? round(($churnCandidates->count() / $totalUsers) * 180, 2) : 0; 

        return view('admin.analytics.loyalty', compact(
            'totalLiability', 
            'tierDistribution', 
            'churnCandidates',
            'churnRate'
        ));
    }
}