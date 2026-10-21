<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RewardRedemption extends Model
{
    protected $fillable = [
        'user_id', 'reward_id', 'points_spent', 'redemption_code',
        'status', 'order_id', 'fulfilled_at',
    ];

    protected $casts = [
        'fulfilled_at' => 'datetime',
    ];

    const STATUS_PENDING   = 'pending';
    const STATUS_FULFILLED = 'fulfilled';
    const STATUS_EXPIRED   = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public static function generateCode(): string
    {
        do {
            $code = 'RWD-' . Str::upper(Str::random(8));
        } while (static::where('redemption_code', $code)->exists());

        return $code;
    }
}
