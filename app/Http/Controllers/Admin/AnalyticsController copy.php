<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, PointsTransaction, Tier, RewardRedemption, Order, PointsCampaign};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /** View Points Liability Report — total outstanding points = future redemption cost */
    public function pointsLiability()
    {
        $totalOutstanding = User::sum('points_balance');

        $byTier = User::join('tiers', 'users.tier_id', '=', 'tiers.id')
            ->select('tiers.name', DB::raw('SUM(users.points_balance) as total_points'), DB::raw('COUNT(users.id) as member_count'))
            ->groupBy('tiers.id', 'tiers.name')
            ->get();

        $expiringNext30Days = PointsTransaction::expiringSoon(30)->sum('points');

        return view('admin.analytics.points-liability', compact('totalOutstanding', 'byTier', 'expiringNext30Days'));
    }

    /** View Tier Distribution — member count and % per tier */
    public function tierDistribution()
    {
        $distribution = Tier::withCount('users')->orderBy('level')->get()->map(function ($tier) {
            $total = User::whereNotNull('tier_id')->count();
            return [
                'tier'       => $tier,
                'count'      => $tier->users_count,
                'percentage' => $total > 0 ? round(($tier->users_count / $total) * 100, 1) : 0,
            ];
        });

        return view('admin.analytics.tier-distribution', compact('distribution'));
    }

    /** View Redemption Rate — % of earned points actually redeemed */
    public function redemptionRate()
    {
        $totalEarned = PointsTransaction::where('type', PointsTransaction::TYPE_EARN)->sum('points');
        $totalBurned = abs(PointsTransaction::where('type', PointsTransaction::TYPE_BURN)->sum('points'));

        $rate = $totalEarned > 0 ? round(($totalBurned / $totalEarned) * 100, 2) : 0;

        $topRewards = RewardRedemption::selectRaw('reward_id, COUNT(*) as total')
            ->groupBy('reward_id')
            ->with('reward')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('admin.analytics.redemption-rate', compact('rate', 'totalEarned', 'totalBurned', 'topRewards'));
    }

    /**
     * View Churn Prediction Score — rule-based engagement scoring.
     * Score 0-100 (lower = higher churn risk) based on recency, frequency, points activity.
     */
    public function churnPrediction()
    {
        $users = User::where('role_id', null) // customers only
            ->withCount('orders')
            ->get()
            ->map(function ($user) {
                $lastOrder = $user->orders()->latest()->first();
                $daysSinceLastOrder = $lastOrder ? $lastOrder->created_at->diffInDays(now()) : 999;

                $score = $this->calculateChurnScore($user, $daysSinceLastOrder);

                return [
                    'user'                  => $user,
                    'days_since_last_order' => $daysSinceLastOrder,
                    'churn_score'           => $score,
                    'risk_level'            => $score < 30 ? 'High' : ($score < 60 ? 'Medium' : 'Low'),
                ];
            })
            ->sortBy('churn_score');

        return view('admin.analytics.churn-prediction', compact('users'));
    }

    protected function calculateChurnScore(User $user, int $daysSinceLastOrder): int
    {
        $score = 100;

        // Recency penalty
        if ($daysSinceLastOrder > 90) $score -= 40;
        elseif ($daysSinceLastOrder > 60) $score -= 25;
        elseif ($daysSinceLastOrder > 30) $score -= 10;

        // Frequency bonus
        $orderCount = $user->orders_count ?? $user->orders()->count();
        if ($orderCount >= 10) $score += 10;
        elseif ($orderCount <= 1) $score -= 15;

        // Points activity bonus (engaged with loyalty program)
        $recentPointsActivity = $user->pointsTransactions()->where('created_at', '>=', now()->subDays(30))->exists();
        if ($recentPointsActivity) $score += 10;

        return max(0, min(100, $score));
    }

    /** View Campaign ROI — points awarded vs revenue generated during campaign window */
    public function campaignRoi(PointsCampaign $campaign)
    {
        $orders = Order::where('created_at', '>=', $campaign->starts_at)
            ->where('created_at', '<=', $campaign->ends_at)
            ->where('status', Order::STATUS_DELIVERED)
            ->get();

        $revenue       = $orders->sum('total');
        $pointsAwarded = $orders->sum('points_earned');
        $orderCount    = $orders->count();

        // Rough cost estimate: assume 1 point = $0.01 redemption value liability
        $estimatedPointsCost = $pointsAwarded * 0.01;
        $roi = $estimatedPointsCost > 0 ? round((($revenue - $estimatedPointsCost) / $estimatedPointsCost) * 100, 1) : 0;

        return view('admin.analytics.campaign-roi', compact('campaign', 'revenue', 'pointsAwarded', 'orderCount', 'roi'));
    }

    /** Export Report (generic CSV export for any analytics view) */
    public function export(Request $request)
    {
        $type = $request->query('type', 'points_liability');
        $filename = "{$type}-report-" . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($type) {
            $handle = fopen('php://output', 'w');

            match ($type) {
                'points_liability'  => $this->exportPointsLiability($handle),
                'tier_distribution'  => $this->exportTierDistribution($handle),
                default              => fputcsv($handle, ['No data']),
            };

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function exportPointsLiability($handle): void
    {
        fputcsv($handle, ['Tier', 'Total Points', 'Member Count']);
        $rows = User::join('tiers', 'users.tier_id', '=', 'tiers.id')
            ->select('tiers.name', DB::raw('SUM(users.points_balance) as total_points'), DB::raw('COUNT(users.id) as member_count'))
            ->groupBy('tiers.id', 'tiers.name')
            ->get();
        foreach ($rows as $row) {
            fputcsv($handle, [$row->name, $row->total_points, $row->member_count]);
        }
    }

    protected function exportTierDistribution($handle): void
    {
        fputcsv($handle, ['Tier', 'Member Count']);
        foreach (Tier::withCount('users')->orderBy('level')->get() as $tier) {
            fputcsv($handle, [$tier->name, $tier->users_count]);
        }
    }
}
