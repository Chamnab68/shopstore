<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    protected $fillable = [
        'name', 'level', 'min_spend', 'min_points', 'point_earn_multiplier',
        'free_shipping', 'priority_support', 'exclusive_access',
        'badge_color', 'is_active',
    ];

    protected $casts = [
        'free_shipping'     => 'boolean',
        'priority_support'  => 'boolean',
        'exclusive_access'  => 'boolean',
        'is_active'         => 'boolean',
        'point_earn_multiplier' => 'decimal:1',
    ];

    const SILVER   = 1;
    const GOLD     = 2;
    const PLATINUM = 3;

    public function users()
    {
        return $this->hasMany(User::class, 'tier_id');
    }

    public function benefits()
    {
        return $this->hasMany(TierBenefit::class);
    }

    public function rewards()
    {
        return $this->hasMany(Reward::class, 'min_tier_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isHigherThan(Tier $other): bool
    {
        return $this->level > $other->level;
    }
}
