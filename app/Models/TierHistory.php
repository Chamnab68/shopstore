<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TierHistory extends Model
{
    protected $table = 'tier_histories';

    protected $fillable = [
        'user_id', 'from_tier_id', 'to_tier_id',
        'change_type', 'reason', 'changed_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromTier()
    {
        return $this->belongsTo(Tier::class, 'from_tier_id');
    }

    public function toTier()
    {
        return $this->belongsTo(Tier::class, 'to_tier_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function isManualOverride(): bool
    {
        return $this->change_type === 'manual_override';
    }
}
