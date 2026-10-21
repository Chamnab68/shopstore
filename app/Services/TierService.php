<?php

namespace App\Services;

use App\Models\{Tier, TierHistory, User};
use App\Services\ActivityLogService;

/**
 * Module #6 — Tier & Membership Management.
 * Handles tier evaluation, auto upgrade/downgrade, and manual override.
 */
class TierService
{
    public function __construct(protected ActivityLogService $activityLog) {}

    /**
     * Evaluate a user's spend/points against tier thresholds.
     * Call this after every delivered order or on a scheduled quarterly review.
     */
    public function evaluate(User $user): ?Tier
    {
        $qualifyingTier = Tier::active()
            ->where('min_spend', '<=', $this->getUserLifetimeSpend($user))
            ->where('min_points', '<=', $user->lifetime_points)
            ->orderByDesc('level')
            ->first();

        if (!$qualifyingTier) {
            return $user->tier;
        }

        $currentTier = $user->tier;

        if (!$currentTier || $qualifyingTier->level > $currentTier->level) {
            $this->changeTier($user, $qualifyingTier, 'upgrade');
        } elseif ($qualifyingTier->level < $currentTier->level) {
            $this->changeTier($user, $qualifyingTier, 'downgrade');
        }

        return $qualifyingTier;
    }

    /** Admin manual override — requires reason for audit */
    public function manualOverride(User $user, Tier $newTier, string $reason, int $adminId): TierHistory
    {
        return $this->changeTier($user, $newTier, 'manual_override', $reason, $adminId);
    }

    /** Run scheduled quarterly tier review for all users */
    public function runQuarterlyReview(): int
    {
        $reviewed = 0;
        User::whereNotNull('tier_id')->chunk(200, function ($users) use (&$reviewed) {
            foreach ($users as $user) {
                $this->evaluate($user);
                $reviewed++;
            }
        });
        return $reviewed;
    }

    protected function changeTier(
        User $user,
        Tier $newTier,
        string $changeType,
        ?string $reason = null,
        ?int $changedBy = null
    ): TierHistory {
        $fromTierId = $user->tier_id;

        $user->update([
            'tier_id'   => $newTier->id,
            'tier_since' => now(),
        ]);

        $history = TierHistory::create([
            'user_id'      => $user->id,
            'from_tier_id' => $fromTierId,
            'to_tier_id'   => $newTier->id,
            'change_type'  => $changeType,
            'reason'       => $reason,
            'changed_by'   => $changedBy,
        ]);

        $this->activityLog->log('tier.' . $changeType, $user, [
            'old_values' => ['tier_id' => $fromTierId],
            'new_values' => ['tier_id' => $newTier->id],
            'metadata'   => ['reason' => $reason],
        ]);

        return $history;
    }

    /** Sum of delivered order totals for tier qualification */
    protected function getUserLifetimeSpend(User $user): float
    {
        return $user->orders()->where('status', 'delivered')->sum('total');
    }
}
