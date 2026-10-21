<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'image',
        'price', 'sale_price', 'stock', 'badge', 'rating',
        'is_active', 'is_featured', 'sold_count',
        // loyalty fields
        'is_bonus_point', 'point_multiplier', 'is_reward_only', 'point_cost',
    ];

    protected $casts = [
        'price'            => 'decimal:2',
        'sale_price'       => 'decimal:2',
        'is_active'        => 'boolean',
        'is_featured'      => 'boolean',
        'is_bonus_point'   => 'boolean',
        'is_reward_only'   => 'boolean',
        'point_multiplier' => 'decimal:1',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function rewards()
    {
        return $this->hasMany(Reward::class);
    }

    public function pointsCampaigns()
    {
        return $this->hasMany(PointsCampaign::class);
    }

    // ── Accessors ────────────────────────────────────────────────────────────
    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if (!$this->sale_price) {
            return 0;
        }
        return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    // ── Scopes ──────────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePurchasable($query)
    {
        // reward-only items cannot be bought with cash
        return $query->where('is_reward_only', false);
    }

    public function scopeRewardOnly($query)
    {
        return $query->where('is_reward_only', true);
    }

    public function scopeBonusPoint($query)
    {
        return $query->where('is_bonus_point', true);
    }

    // ── Loyalty helpers ───────────────────────────────────────────────────────
    /** Effective point multiplier for this product (combines bonus flag + multiplier) */
    public function getEffectiveMultiplier(): float
    {
        return $this->is_bonus_point ? (float) $this->point_multiplier : 1.0;
    }
}
