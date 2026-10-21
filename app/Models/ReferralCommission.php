<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralCommission extends Model
{
    protected $fillable = [
        'referral_id', 'order_id', 'level', 'points_awarded',
        'cash_commission', 'payout_status', 'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    const PAYOUT_PENDING   = 'pending';
    const PAYOUT_PAID      = 'paid';
    const PAYOUT_CANCELLED = 'cancelled';

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
