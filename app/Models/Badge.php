<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = [
        'name', 'icon', 'description', 'trigger_type', 'trigger_value', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    const TRIGGER_FIRST_PURCHASE      = 'first_purchase';
    const TRIGGER_ORDER_MILESTONE     = 'order_milestone';
    const TRIGGER_REFERRAL_MILESTONE  = 'referral_milestone';
    const TRIGGER_LOGIN_STREAK        = 'login_streak';
    const TRIGGER_POINTS_MILESTONE    = 'points_milestone';
    const TRIGGER_MANUAL              = 'manual';

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')->withPivot('awarded_at', 'is_displayed');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
