<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TierBenefit extends Model
{
    protected $fillable = ['tier_id', 'benefit_key', 'benefit_value', 'description'];

    public function tier()
    {
        return $this->belongsTo(Tier::class);
    }
}
