<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Referral extends Model
{
    protected $fillable = [
        'referral_program_id', 'referrer_id', 'referee_id',
        'referral_link_code', 'status', 'registered_at', 'converted_at',
        'ip_address', 'is_flagged_fraud', 'fraud_reason',
    ];

    protected $casts = [
        'registered_at'    => 'datetime',
        'converted_at'     => 'datetime',
        'is_flagged_fraud' => 'boolean',
    ];

    const STATUS_PENDING        = 'pending';
    const STATUS_REGISTERED     = 'registered';
    const STATUS_CONVERTED      = 'converted';
    const STATUS_REJECTED_FRAUD = 'rejected_fraud';

    public function program()
    {
        return $this->belongsTo(ReferralProgram::class, 'referral_program_id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referee()
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    public function commissions()
    {
        return $this->hasMany(ReferralCommission::class);
    }

    public static function generateLinkCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (static::where('referral_link_code', $code)->exists());

        return $code;
    }

    /** Basic fraud check: same IP as referrer, or self-referral */
    public function checkFraud(?string $refereeIp = null): bool
    {
        if ($this->referrer_id === $this->referee_id) {
            return true;
        }
        if ($refereeIp && $this->ip_address && $refereeIp === $this->ip_address) {
            return true;
        }
        return false;
    }
}
