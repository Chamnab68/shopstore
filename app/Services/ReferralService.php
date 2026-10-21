<?php

namespace App\Services;

use App\Models\{Referral, ReferralProgram, ReferralCommission, User, Order};
use App\Services\{PointsEngineService, ActivityLogService};
use Illuminate\Support\Facades\DB;

/**
 * Module #9 — Referral & Affiliate Program Management.
 * Handles link generation, conversion tracking, fraud detection, and payouts.
 */
class ReferralService
{
    public function __construct(
        protected PointsEngineService $pointsEngine,
        protected ActivityLogService $activityLog
    ) {}

    /** Generate a unique referral link for a user under the active program */
    public function generateLink(User $referrer): Referral
    {
        $program = ReferralProgram::active()->first();
        if (!$program) {
            throw new \RuntimeException('No active referral program configured.');
        }

        return Referral::create([
            'referral_program_id' => $program->id,
            'referrer_id'         => $referrer->id,
            'referral_link_code'  => Referral::generateLinkCode(),
            'status'              => Referral::STATUS_PENDING,
            'ip_address'          => request()->ip(),
        ]);
    }

    /** Called when a new user registers using a referral code */
    public function trackRegistration(string $linkCode, User $newUser): ?Referral
    {
        $referral = Referral::where('referral_link_code', $linkCode)
            ->where('status', Referral::STATUS_PENDING)
            ->first();

        if (!$referral) {
            return null;
        }

        $referral->update([
            'referee_id'     => $newUser->id,
            'status'         => Referral::STATUS_REGISTERED,
            'registered_at'  => now(),
        ]);

        // Fraud check: self-referral or same IP
        if ($referral->checkFraud(request()->ip())) {
            $referral->update([
                'status'           => Referral::STATUS_REJECTED_FRAUD,
                'is_flagged_fraud' => true,
                'fraud_reason'     => 'Self-referral or duplicate IP detected',
            ]);
            $this->activityLog->log('referral.fraud_detected', $newUser, [
                'metadata' => ['referral_id' => $referral->id],
            ]);
            return $referral;
        }

        $newUser->update(['referred_by' => $referral->referrer_id]);

        // Award immediate signup bonus to referee if program does not require first purchase
        $program = $referral->program;
        if (!$program->requires_first_purchase) {
            $this->convert($referral);
        }

        return $referral;
    }

    /** Called when the referee completes their first qualifying purchase */
    public function trackConversion(Order $order): ?Referral
    {
        $user = $order->user;
        $referral = $user->referralReceived;

        if (!$referral || $referral->status !== Referral::STATUS_REGISTERED) {
            return null;
        }

        if ($order->orders_count_before_this ?? $user->orders()->where('status', 'delivered')->count() > 1) {
            // Only the first delivered order triggers conversion
        }

        return $this->convert($referral, $order);
    }

    protected function convert(Referral $referral, ?Order $order = null): Referral
    {
        return DB::transaction(function () use ($referral, $order) {
            $program = $referral->program;

            $referral->update([
                'status'        => Referral::STATUS_CONVERTED,
                'converted_at'  => now(),
            ]);

            // Level 1: direct referrer reward
            $this->payCommission($referral, 1, $program->referrer_reward_points, 0, $order);
            $this->pointsEngine->manualAdjust(
                $referral->referrer,
                $program->referrer_reward_points,
                'Referral conversion bonus',
                $referral->referrer_id
            );

            // Referee welcome bonus
            if ($referral->referee && $program->referee_reward_points > 0) {
                $this->pointsEngine->manualAdjust(
                    $referral->referee,
                    $program->referee_reward_points,
                    'Referral signup bonus',
                    $referral->referee_id
                );
            }

            // Level 2: multi-level commission to the referrer's own referrer
            if ($program->isMultiLevel() && $referral->referrer->referred_by) {
                $level2Referral = Referral::where('referee_id', $referral->referrer_id)
                    ->where('status', Referral::STATUS_CONVERTED)
                    ->first();

                if ($level2Referral) {
                    $level2Points = (int) round($program->referrer_reward_points * ($program->level2_commission_percent / 100));
                    if ($level2Points > 0) {
                        $this->payCommission($level2Referral, 2, $level2Points, 0, $order);
                        $this->pointsEngine->manualAdjust(
                            $level2Referral->referrer,
                            $level2Points,
                            'Level-2 referral commission',
                            $level2Referral->referrer_id
                        );
                    }
                }
            }

            $this->activityLog->log('referral.converted', $referral->referrer, [
                'metadata' => ['referral_id' => $referral->id, 'order_id' => $order?->id],
            ]);

            return $referral;
        });
    }

    protected function payCommission(Referral $referral, int $level, int $points, float $cash, ?Order $order): ReferralCommission
    {
        return ReferralCommission::create([
            'referral_id'      => $referral->id,
            'order_id'         => $order?->id,
            'level'            => $level,
            'points_awarded'   => $points,
            'cash_commission'  => $cash,
            'payout_status'    => ReferralCommission::PAYOUT_PAID,
            'paid_at'          => now(),
        ]);
    }

    /** Admin: get referral stats for dashboard */
    public function getStats(): array
    {
        return [
            'total_referrals'  => Referral::count(),
            'pending'          => Referral::where('status', Referral::STATUS_PENDING)->count(),
            'registered'       => Referral::where('status', Referral::STATUS_REGISTERED)->count(),
            'converted'        => Referral::where('status', Referral::STATUS_CONVERTED)->count(),
            'flagged_fraud'    => Referral::where('is_flagged_fraud', true)->count(),
            'conversion_rate'  => $this->getConversionRate(),
        ];
    }

    protected function getConversionRate(): float
    {
        $registered = Referral::where('status', '!=', Referral::STATUS_PENDING)->count();
        if ($registered === 0) return 0.0;
        $converted = Referral::where('status', Referral::STATUS_CONVERTED)->count();
        return round(($converted / $registered) * 100, 2);
    }
}
