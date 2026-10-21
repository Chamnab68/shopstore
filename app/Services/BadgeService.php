<?php

namespace App\Services;

use App\Models\{Badge, User, UserBadge, Order, Referral};
use App\Services\ActivityLogService;

/**
 * Module #8 — Gamification & Badge Management.
 * Checks trigger conditions and auto-awards badges.
 */
class BadgeService
{
    public function __construct(protected ActivityLogService $activityLog) {}

    /** Call after an order is delivered */
    public function checkOrderTriggers(User $user, Order $order): array
    {
        $awarded = [];

        $deliveredCount = $user->orders()->where('status', 'delivered')->count();

        if ($deliveredCount === 1) {
            $awarded[] = $this->award($user, Badge::TRIGGER_FIRST_PURCHASE);
        }

        $milestoneBadges = Badge::active()
            ->where('trigger_type', Badge::TRIGGER_ORDER_MILESTONE)
            ->where('trigger_value', '<=', $deliveredCount)
            ->get();

        foreach ($milestoneBadges as $badge) {
            $awarded[] = $this->awardBadge($user, $badge);
        }

        return array_filter($awarded);
    }

    /** Call after a referral conversion */
    public function checkReferralTriggers(User $referrer): array
    {
        $awarded = [];
        $conversions = $referrer->referralsMade()->where('status', Referral::STATUS_CONVERTED)->count();

        $milestoneBadges = Badge::active()
            ->where('trigger_type', Badge::TRIGGER_REFERRAL_MILESTONE)
            ->where('trigger_value', '<=', $conversions)
            ->get();

        foreach ($milestoneBadges as $badge) {
            $awarded[] = $this->awardBadge($referrer, $badge);
        }

        return array_filter($awarded);
    }

    /** Call after points are earned */
    public function checkPointsTriggers(User $user): array
    {
        $awarded = [];

        $milestoneBadges = Badge::active()
            ->where('trigger_type', Badge::TRIGGER_POINTS_MILESTONE)
            ->where('trigger_value', '<=', $user->lifetime_points)
            ->get();

        foreach ($milestoneBadges as $badge) {
            $awarded[] = $this->awardBadge($user, $badge);
        }

        return array_filter($awarded);
    }

    /** Track consecutive-day login streaks */
    public function trackLoginStreak(User $user): void
    {
        $today = now()->startOfDay();
        $lastLogin = $user->login_streak_updated_at?->startOfDay();

        if ($lastLogin && $lastLogin->diffInDays($today) === 1) {
            $user->increment('login_streak_count');
        } elseif (!$lastLogin || $lastLogin->diffInDays($today) > 1) {
            $user->update(['login_streak_count' => 1]);
        }
        $user->update(['login_streak_updated_at' => now()]);

        $streakBadges = Badge::active()
            ->where('trigger_type', Badge::TRIGGER_LOGIN_STREAK)
            ->where('trigger_value', '<=', $user->login_streak_count)
            ->get();

        foreach ($streakBadges as $badge) {
            $this->awardBadge($user, $badge);
        }
    }

    /** Manual award by admin */
    public function manualAward(User $user, Badge $badge, int $adminId): ?UserBadge
    {
        return $this->awardBadge($user, $badge);
    }

    protected function award(User $user, string $triggerType): ?UserBadge
    {
        $badge = Badge::active()->where('trigger_type', $triggerType)->first();
        return $badge ? $this->awardBadge($user, $badge) : null;
    }

    protected function awardBadge(User $user, Badge $badge): ?UserBadge
    {
        // Prevent duplicate awards (unique constraint also enforces this)
        if (UserBadge::where('user_id', $user->id)->where('badge_id', $badge->id)->exists()) {
            return null;
        }

        $userBadge = UserBadge::create([
            'user_id'    => $user->id,
            'badge_id'   => $badge->id,
            'awarded_at' => now(),
        ]);

        $this->activityLog->log('badge.awarded', $user, [
            'metadata' => ['badge_id' => $badge->id, 'badge_name' => $badge->name],
        ]);

        return $userBadge;
    }
}
