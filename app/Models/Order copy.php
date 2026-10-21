<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'user_id', 'status', 'payment_status', 'payment_method',
        'subtotal', 'discount', 'shipping_fee', 'total',
        'shipping_name', 'shipping_phone', 'shipping_address', 'notes',
        'receipt_image', 'receipt_uploaded_at', 'admin_notes',
        'approved_by', 'approved_at', 'canceled_at',
        // loyalty fields
        'points_earned', 'points_used', 'points_discount', 'points_awarded',
    ];

    protected $casts = [
        'subtotal'             => 'decimal:2',
        'discount'             => 'decimal:2',
        'shipping_fee'         => 'decimal:2',
        'total'                => 'decimal:2',
        'points_discount'      => 'decimal:2',
        'points_awarded'       => 'boolean',
        'receipt_uploaded_at'  => 'datetime',
        'approved_at'          => 'datetime',
        'canceled_at'          => 'datetime',
    ];

    // ── Status constants ────────────────────────────────────────────────────
    const STATUS_PENDING    = 'pending';
    const STATUS_CONFIRMED  = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_CANCELLED  = 'cancelled';

    const PAYMENT_UNPAID    = 'unpaid';
    const PAYMENT_PAID      = 'paid';
    const PAYMENT_VERIFYING = 'pending_verification';
    const PAYMENT_REFUNDED  = 'refunded';

    const METHOD_BANK_TRANSFER    = 'bank_transfer';
    const METHOD_CASH_ON_DELIVERY = 'cash_on_delivery';

    // ── Relationships ────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function pointsTransactions()
    {
        return $this->hasMany(PointsTransaction::class);
    }

    public function rewardRedemption()
    {
        return $this->hasOne(RewardRedemption::class);
    }

    public function referralCommissions()
    {
        return $this->hasMany(ReferralCommission::class);
    }

    // ── Order number generator ───────────────────────────────────────────────
    public static function generateOrderNumber(): string
    {
        $last   = static::latest('id')->first();
        $number = $last ? (intval(substr($last->order_number, 4)) + 1) : 1;
        return 'ORD-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    // ── Status helpers ───────────────────────────────────────────────────────
    public function isPending(): bool   { return $this->status === self::STATUS_PENDING; }
    public function isConfirmed(): bool { return $this->status === self::STATUS_CONFIRMED; }
    public function isDelivered(): bool { return $this->status === self::STATUS_DELIVERED; }
    public function isCancelled(): bool { return $this->status === self::STATUS_CANCELLED; }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function isPaid(): bool          { return $this->payment_status === self::PAYMENT_PAID; }
    public function isUnpaid(): bool        { return $this->payment_status === self::PAYMENT_UNPAID; }
    public function isBankTransfer(): bool  { return $this->payment_method === self::METHOD_BANK_TRANSFER; }
    public function isCashOnDelivery(): bool{ return $this->payment_method === self::METHOD_CASH_ON_DELIVERY; }

    /** Order qualifies for points (only count delivered, non-cancelled orders) */
    public function qualifiesForPoints(): bool
    {
        return $this->isDelivered() && !$this->points_awarded;
    }

    // ── Badge accessors ──────────────────────────────────────────────────────
    public function getStatusBadgeAttribute(): string
    {
        $config = [
            self::STATUS_PENDING    => ['warning text-dark', 'fa-clock',         'Pending'],
            self::STATUS_CONFIRMED  => ['info text-white',   'fa-check',         'Confirmed'],
            self::STATUS_PROCESSING => ['primary text-white','fa-cog',           'Processing'],
            self::STATUS_SHIPPED    => ['secondary text-white','fa-truck',        'Shipped'],
            self::STATUS_DELIVERED  => ['success text-white','fa-check-circle',  'Delivered'],
            self::STATUS_CANCELLED  => ['danger text-white', 'fa-times-circle',  'Cancelled'],
        ];
        [$color, $icon, $label] = $config[$this->status] ?? ['light text-dark', 'fa-question', 'Unknown'];
        return "<span class=\"badge bg-{$color} px-2 py-1\"><i class=\"fas {$icon} me-1\"></i>{$label}</span>";
    }

    public function getPaymentBadgeAttribute(): string
    {
        $config = [
            self::PAYMENT_UNPAID    => ['danger text-white',   'fa-times',     'Unpaid'],
            self::PAYMENT_PAID      => ['success text-white',  'fa-check',     'Paid'],
            self::PAYMENT_VERIFYING => ['warning text-dark',   'fa-hourglass', 'Verifying'],
            self::PAYMENT_REFUNDED  => ['secondary text-white','fa-undo',      'Refunded'],
        ];
        [$color, $icon, $label] = $config[$this->payment_status] ?? ['light text-dark', 'fa-question', 'Unknown'];
        return "<span class=\"badge bg-{$color} px-2 py-1\"><i class=\"fas {$icon} me-1\"></i>{$label}</span>";
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            self::METHOD_BANK_TRANSFER    => 'Bank Transfer',
            self::METHOD_CASH_ON_DELIVERY => 'Cash on Delivery',
            default                       => ucfirst(str_replace('_', ' ', $this->payment_method)),
        };
    }

    // ── Scopes ──────────────────────────────────────────────────────────────
    public function scopePending($query)   { return $query->where('status', self::STATUS_PENDING); }
    public function scopeDelivered($query) { return $query->where('status', self::STATUS_DELIVERED); }
    public function scopeCancelled($query) { return $query->where('status', self::STATUS_CANCELLED); }
    public function scopeForUser($query, $userId) { return $query->where('user_id', $userId); }
    public function scopeUnawardedPoints($query)
    {
        return $query->where('points_awarded', false)->where('status', self::STATUS_DELIVERED);
    }
}
