<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralProgram extends Model
{
    protected $fillable = [
        'name', 'referrer_reward_points', 'referee_reward_points',
        'commission_percent', 'max_levels', 'level2_commission_percent',
        'requires_first_purchase', 'is_active',
    ];

    protected $casts = [
        'requires_first_purchase' => 'boolean',
        'is_active'               => 'boolean',
    ];

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isMultiLevel(): bool
    {
        return $this->max_levels > 1;
    }
}
