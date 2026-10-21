<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointsRule extends Model
{
    protected $fillable = [
        'name', 'type', 'points_per_dollar', 'multiplier', 'category_id',
        'min_order_amount', 'active_days', 'starts_at', 'ends_at',
        'is_active', 'priority',
    ];

    protected $casts = [
        'active_days' => 'array',
        'starts_at'   => 'date',
        'ends_at'     => 'date',
        'is_active'   => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(PointsTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    /** Check if this rule applies today (for weekend_bonus type) */
    public function appliesToday(): bool
    {
        if (empty($this->active_days)) {
            return true;
        }
        $today = strtolower(now()->format('l'));
        return in_array($today, $this->active_days);
    }
}
