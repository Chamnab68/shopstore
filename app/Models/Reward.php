<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = [
        'name', 'description', 'image', 'type', 'product_id', 'point_cost',
        'min_tier_id', 'total_stock', 'redeemed_count', 'per_member_limit',
        'valid_from', 'valid_until', 'is_active',
    ];

    protected $casts = [
        'valid_from'  => 'date',
        'valid_until' => 'date',
        'is_active'   => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function minTier()
    {
        return $this->belongsTo(Tier::class, 'min_tier_id');
    }

    public function redemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            });
    }

    public function isInStock(): bool
    {
        return $this->total_stock === null || $this->redeemed_count < $this->total_stock;
    }

    public function isAvailableForUser(User $user): bool
    {
        if (!$this->is_active || !$this->isInStock()) {
            return false;
        }
        if ($this->min_tier_id && $user->tier_id < $this->min_tier_id) {
            return false;
        }
        if ($this->per_member_limit) {
            $userRedemptions = $this->redemptions()->where('user_id', $user->id)->count();
            if ($userRedemptions >= $this->per_member_limit) {
                return false;
            }
        }
        return true;
    }
}
