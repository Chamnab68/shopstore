<?php

namespace App\Services;

use App\Models\{Order, PointsRule, PointsCampaign, PointsTransaction, User};
use Illuminate\Support\Facades\DB;

/**
 * Module #5 — Loyalty Points Engine.
 * Handles earning, burning, expiring, and manually adjusting points.
 */
class PointsEngineService
{
    /**
     * Calculate and award points for a delivered order.
     * Applies: base rate -> tier multiplier -> active campaign multiplier -> per-product bonus.
     */
    public function awardPointsForOrder(Order $order): int
    {
        if (!$order->qualifiesForPoints()) {
            return 0;
        }

        $user = $order->user;
        $baseRule = PointsRule::active()->where('type', 'earn_rate')->orderByDesc('priority')->first();
        $pointsPerDollar = $baseRule->points_per_dollar ?? 1.0;

        $totalPoints = 0;

        foreach ($order->items as $item) {
            $product = $item->product;
            $lineTotal = $item->subtotal;

            $multiplier = 1.0;

            // Tier multiplier
            if ($user->tier) {
                $multiplier *= (float) $user->tier->point_earn_multiplier;
            }

            // Product-level bonus multiplier
            if ($product && $product->is_bonus_point) {
                $multiplier *= (float) $product->point_multiplier;
            }

            // Active campaign multiplier (category/product/tier scoped)
            $campaign = $this->findActiveCampaign($product, $user);
            if ($campaign) {
                $multiplier *= (float) $campaign->multiplier;
            }

            // Weekend bonus rule
            $weekendRule = PointsRule::active()->where('type', 'weekend_bonus')->first();
            if ($weekendRule && $weekendRule->appliesToday()) {
                $multiplier *= (float) $weekendRule->multiplier;
            }

            $linePoints = (int) floor($lineTotal * $pointsPerDollar * $multiplier);
            $totalPoints += $linePoints;
        }

        if ($totalPoints > 0) {
            $this->creditPoints($user, $totalPoints, PointsTransaction::TYPE_EARN, [
                'order_id'       => $order->id,
                'points_rule_id' => $baseRule->id ?? null,
                'expires_at'     => $this->calculateExpiry(),
            ]);

            $order->update(['points_earned' => $totalPoints, 'points_awarded' => true]);
        }

        return $totalPoints;
    }

    /** Burn points to redeem a reward or apply checkout discount */
    public function burnPoints(User $user, int $points, string $reason = null, array $extra = []): PointsTransaction
    {
        if ($user->points_balance < $points) {
            throw new \RuntimeException('Insufficient points balance.');
        }

        return $this->debitPoints($user, $points, PointsTransaction::TYPE_BURN, array_merge([
            'reason' => $reason,
        ], $extra));
    }

    /** Manual points adjustment by admin — mandatory reason logged */
    public function manualAdjust(User $user, int $points, string $reason, int $adminId): PointsTransaction
    {
        $type = $points >= 0 ? PointsTransaction::TYPE_EARN : PointsTransaction::TYPE_MANUAL_ADJUST;

        return $points >= 0
            ? $this->creditPoints($user, abs($points), PointsTransaction::TYPE_MANUAL_ADJUST, [
                'reason'      => $reason,
                'adjusted_by' => $adminId,
              ])
            : $this->debitPoints($user, abs($points), PointsTransaction::TYPE_MANUAL_ADJUST, [
                'reason'      => $reason,
                'adjusted_by' => $adminId,
              ]);
    }

    /** Expire points older than their expires_at date (run via scheduled command) */
    public function expirePoints(): int
    {
        $expiredCount = 0;

        $expiring = PointsTransaction::where('type', PointsTransaction::TYPE_EARN)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiring->groupBy('user_id') as $userId => $transactions) {
            $user = User::find($userId);
            if (!$user) continue;

            $totalToExpire = $transactions->sum('points');
            if ($totalToExpire > 0 && $user->points_balance >= $totalToExpire) {
                $this->debitPoints($user, $totalToExpire, PointsTransaction::TYPE_EXPIRE, [
                    'reason' => 'Points expired after retention period',
                ]);
                $expiredCount++;
            }
        }

        return $expiredCount;
    }

    // ── Internal helpers ────────────────────────────────────────────────────
    protected function creditPoints(User $user, int $points, string $type, array $extra = []): PointsTransaction
    {
        return DB::transaction(function () use ($user, $points, $type, $extra) {
            $user->increment('points_balance', $points);
            $user->increment('lifetime_points', $points);

            return PointsTransaction::create(array_merge([
                'user_id'       => $user->id,
                'type'          => $type,
                'points'        => $points,
                'balance_after' => $user->points_balance,
            ], $extra));
        });
    }

    protected function debitPoints(User $user, int $points, string $type, array $extra = []): PointsTransaction
    {
        return DB::transaction(function () use ($user, $points, $type, $extra) {
            $user->decrement('points_balance', $points);

            return PointsTransaction::create(array_merge([
                'user_id'       => $user->id,
                'type'          => $type,
                'points'        => -$points,
                'balance_after' => $user->points_balance,
            ], $extra));
        });
    }

    protected function findActiveCampaign($product, User $user): ?PointsCampaign
    {
        $query = PointsCampaign::currentlyActive();

        if ($product) {
            $query->where(function ($q) use ($product) {
                $q->where('product_id', $product->id)
                  ->orWhere('category_id', $product->category_id)
                  ->orWhereNull('product_id')->whereNull('category_id');
            });
        }

        return $query->where(function ($q) use ($user) {
            $q->whereNull('tier_id')->orWhere('tier_id', $user->tier_id);
        })->orderByDesc('multiplier')->first();
    }

    protected function calculateExpiry(): \Carbon\Carbon
    {
        // Default policy: points expire 12 months after earning
        return now()->addMonths(12);
    }
}
