<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'role_id', 'tier_id', 'name', 'email', 'phone', 'password',
        'points_balance', 'lifetime_points', 'referral_code', 'referred_by',
        'tier_since', 'login_streak_updated_at', 'login_streak_count',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'        => 'datetime',
        'tier_since'                => 'datetime',
        'login_streak_updated_at'   => 'datetime',
        'password'                  => 'hashed',
    ];

    // ── Loyalty-platform relationships ─────────────────────────────────────
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function tier()
    {
        return $this->belongsTo(Tier::class);
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referredUsers()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function pointsTransactions()
    {
        return $this->hasMany(PointsTransaction::class);
    }

    public function tierHistories()
    {
        return $this->hasMany(TierHistory::class);
    }

    public function rewardRedemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')->withPivot('awarded_at', 'is_displayed');
    }

    public function referralsMade()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referralReceived()
    {
        return $this->hasOne(Referral::class, 'referee_id');
    }

    // ── Original ShopStore relationships (kept) ────────────────────────────
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    // ── Role helpers ─────────────────────────────────────────────────────────
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function hasPermission(string $permissionName): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->hasPermission($permissionName);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(Role::SUPER_ADMIN);
    }

    // ── Tier helpers ──────────────────────────────────────────────────────────
    public function canAccessTierFeature(int $minTierId): bool
    {
        return $this->tier_id >= $minTierId;
    }

    // ── Boot: auto-generate referral code ────────────────────────────────────
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                do {
                    $code = Str::upper(Str::random(8));
                } while (static::where('referral_code', $code)->exists());
                $user->referral_code = $code;
            }
        });
    }
}
