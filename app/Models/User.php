<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'address', 'role', 'avatar', 'is_active', 'is_admin',
        'role_id', 'tier_id', 'points_balance', 'lifetime_points',
        'referral_code', 'referred_by', 'tier_since',
        'login_streak_updated_at', 'login_streak_count',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'       => 'datetime',
        'password'                => 'hashed',
        'is_active'               => 'boolean',
        'is_admin'                => 'boolean',
        'tier_since'              => 'datetime',
        'login_streak_updated_at' => 'datetime',
    ];

    // =========================================================================
    // ── RELATIONSHIPS
    // =========================================================================

    public function orders() { return $this->hasMany(Order::class); }
    public function carts() { return $this->hasMany(Cart::class); }
    public function apiKeys() { return $this->hasMany(ApiKey::class); }
    public function roles() { return $this->belongsToMany(Role::class, 'role_user'); }
    public function roleModel() { return $this->belongsTo(Role::class, 'role_id'); }
    public function tier() { return $this->belongsTo(Tier::class, 'tier_id'); }
    public function referredBy() { return $this->belongsTo(User::class, 'referred_by'); }
    public function referredUsers() { return $this->hasMany(User::class, 'referred_by'); }
    public function pointsTransactions() { return $this->hasMany(PointsTransaction::class); }
    public function tierHistories() { return $this->hasMany(TierHistory::class); }
    public function rewardRedemptions() { return $this->hasMany(RewardRedemption::class); }
    public function referralsMade() { return $this->hasMany(Referral::class, 'referrer_id'); }
    public function referralReceived() { return $this->hasOne(Referral::class, 'referee_id'); }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot('awarded_at', 'is_displayed')
                    ->withTimestamps();
    }

    // =========================================================================
    // ── LOYALTY & PERMISSION LOGIC
    // =========================================================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || 
               $this->is_admin == 1 || 
               ($this->role_id !== null && $this->role_id != 1) || 
               $this->roles()->exists();
    }

    public function isSuperAdmin(): bool
    {
        // ផ្អែកលើការកំណត់សិទ្ធិ Super Admin របស់អ្នក
        return $this->role === 'super-admin' || $this->is_admin == 1;
    }

    public function getBalanceAttribute()
    {
        return $this->pointsTransactions()->sum('points');
    }

    public function evaluateAndUpgradeTier(): void
    {
        $totalSpent = $this->orders()->where('status', 'completed')->sum('total_amount');
        $matchedTier = Tier::where('min_spend_threshold', '<=', $totalSpent)
                            ->orderBy('min_spend_threshold', 'desc')
                            ->first();

        if ($matchedTier && $this->tier_id !== $matchedTier->id) {
            $oldTierId = $this->tier_id;
            $this->update(['tier_id' => $matchedTier->id, 'tier_since' => now()]);

            $this->tierHistories()->create([
                'old_tier_id' => $oldTierId,
                'new_tier_id' => $matchedTier->id,
                'reason' => "System upgrade: Spent $" . number_format($totalSpent, 2),
            ]);
        }
    }

    public function recordLoginStreak(): void
    {
        $today = Carbon::today();
        
        if (!$this->login_streak_updated_at) {
            $this->login_streak_count = 1;
        } else {
            $lastLogin = Carbon::parse($this->login_streak_updated_at)->startOfDay();
            $diffInDays = $today->diffInDays($lastLogin);

            if ($diffInDays === 1) {
                $this->login_streak_count += 1;
            } elseif ($diffInDays > 1) {
                $this->login_streak_count = 1;
            }
        }

        $this->login_streak_updated_at = now();
        $this->save();
    }

    // =========================================================================
    // ── MODEL BOOT EVENTS
    // =========================================================================
    
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

            if (empty($user->tier_id)) {
                $user->tier_id = 1; 
                $user->tier_since = now();
            }
        });
    }
}