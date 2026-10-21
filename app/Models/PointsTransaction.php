<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointsTransaction extends Model
{
    protected $fillable = [
        'user_id', 'type', 'points', 'balance_after', 'order_id',
        'points_rule_id', 'reward_redemption_id', 'reason',
        'adjusted_by', 'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    const TYPE_EARN            = 'earn';
    const TYPE_BURN            = 'burn';
    const TYPE_EXPIRE          = 'expire';
    const TYPE_MANUAL_ADJUST   = 'manual_adjust';
    const TYPE_REFERRAL_BONUS  = 'referral_bonus';
    const TYPE_CAMPAIGN_BONUS  = 'campaign_bonus';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function pointsRule()
    {
        return $this->belongsTo(PointsRule::class);
    }

    public function rewardRedemption()
    {
        return $this->belongsTo(RewardRedemption::class);
    }

    public function adjustedBy()
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('type', self::TYPE_EARN)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    public function isCredit(): bool
    {
        return $this->points > 0;
    }
}
