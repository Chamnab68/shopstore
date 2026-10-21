<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointsCampaign extends Model
{
    protected $fillable = [
        'name', 'description', 'multiplier', 'category_id', 'product_id',
        'tier_id', 'starts_at', 'ends_at', 'is_active', 'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function tier()
    {
        return $this->belongsTo(Tier::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeCurrentlyActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function isLive(): bool
    {
        return $this->is_active && now()->between($this->starts_at, $this->ends_at);
    }
}
