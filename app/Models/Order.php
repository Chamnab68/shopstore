<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // ── Fillable ─────────────────────────────────────────────────────────────
    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'discount',
        'shipping_fee',
        'total',
        'referral_amount_used', // 🟢 បន្ថែមថ្មី
        'remaining_payable',    // 🟢 បន្ថែមថ្មី
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'notes',
        'receipt_image',
        'receipt_uploaded_at',
        'admin_notes',
        'approved_by',
        'approved_at',
        'canceled_at',
    ];

    // ── Casts ─────────────────────────────────────────────────────────────────
    protected $casts = [
        'subtotal'             => 'decimal:2',
        'discount'             => 'decimal:2',
        'shipping_fee'         => 'decimal:2',
        'total'                => 'decimal:2',
        'referral_amount_used' => 'decimal:2', // 🟢 បន្ថែមថ្មី
        'remaining_payable'    => 'decimal:2', // 🟢 បន្ថែមថ្មី
        'receipt_uploaded_at'  => 'datetime',
        'approved_at'          => 'datetime',
        'canceled_at'          => 'datetime',
    ];

    // ── Status Constants ──────────────────────────────────────────────────────
    const STATUS_PENDING    = 'pending';
    const STATUS_CONFIRMED  = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_CANCELLED  = 'cancelled';

    // ── Payment Constants ─────────────────────────────────────────────────────
    const PAYMENT_UNPAID    = 'unpaid';
    const PAYMENT_PAID      = 'paid';
    const PAYMENT_VERIFYING = 'pending_verification';
    const PAYMENT_REFUNDED  = 'refunded';

    // ── Payment Method Constants ──────────────────────────────────────────────
    const METHOD_BANK_TRANSFER    = 'bank_transfer';
    const METHOD_CASH_ON_DELIVERY = 'cash_on_delivery';

    // ── Relationships ─────────────────────────────────────────────────────────
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

    // ── Generate Order Number ─────────────────────────────────────────────────
    public static function generateOrderNumber(): string
    {
        $last   = static::latest('id')->first();
        $number = $last ? (intval(substr($last->order_number, 4)) + 1) : 1;

        return 'ORD-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    // ── Status Helpers ────────────────────────────────────────────────────────
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
        ]);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function isUnpaid(): bool
    {
        return $this->payment_status === self::PAYMENT_UNPAID;
    }

    public function isBankTransfer(): bool
    {
        return $this->payment_method === self::METHOD_BANK_TRANSFER || $this->payment_method === 'referral_mixed_bank_transfer';
    }

    public function isCashOnDelivery(): bool
    {
        return $this->payment_method === self::METHOD_CASH_ON_DELIVERY || $this->payment_method === 'referral_mixed_cash_on_delivery';
    }

    // ── 🟢 Referral Payment Helpers ──────────────────────────────────────────
    public function hasReferralDiscount(): bool
    {
        return $this->referral_amount_used > 0;
    }

    public function isFullyCoveredByReferral(): bool
    {
        return $this->payment_method === 'referral_full';
    }

    // ── Status Badge Accessor ─────────────────────────────────────────────────
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

        [$color, $icon, $label] = $config[$this->status]
            ?? ['light text-dark', 'fa-question', 'Unknown'];

        return "<span class=\"badge bg-{$color} px-2 py-1\">
                    <i class=\"fas {$icon} me-1\"></i>{$label}
                </span>";
    }

    // ── Payment Badge Accessor ────────────────────────────────────────────────
    public function getPaymentBadgeAttribute(): string
    {
        $config = [
            self::PAYMENT_UNPAID    => ['danger text-white',   'fa-times',     'Unpaid'],
            self::PAYMENT_PAID      => ['success text-white',  'fa-check',     'Paid'],
            self::PAYMENT_VERIFYING => ['warning text-dark',   'fa-hourglass', 'Verifying'],
            self::PAYMENT_REFUNDED  => ['secondary text-white','fa-undo',      'Refunded'],
        ];

        [$color, $icon, $label] = $config[$this->payment_status]
            ?? ['light text-dark', 'fa-question', 'Unknown'];

        return "<span class=\"badge bg-{$color} px-2 py-1\">
                    <i class=\"fas {$icon} me-1\"></i>{$label}
                </span>";
    }

    // ── Payment Method Label Accessor (កែសម្រួលដើម្បីគាំទ្រ Referral) ─────────────
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            self::METHOD_BANK_TRANSFER                  => 'Bank Transfer',
            self::METHOD_CASH_ON_DELIVERY               => 'Cash on Delivery',
            'referral_full'                             => 'Referral Balance (100%)',
            'referral_mixed_bank_transfer'              => 'Referral Balance + Bank Transfer',
            'referral_mixed_cash_on_delivery'           => 'Referral Balance + COD',
            default                                     => ucfirst(str_replace('_', ' ', $this->payment_method)),
        };
    }

    // ── Status Label Accessor ─────────────────────────────────────────────────
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING    => 'Pending',
            self::STATUS_CONFIRMED  => 'Confirmed',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED    => 'Shipped',
            self::STATUS_DELIVERED  => 'Delivered',
            self::STATUS_CANCELLED  => 'Cancelled',
            default                 => ucfirst($this->status),
        };
    }

    // ── Scopes ────────────────────────────────────────────────────────────────
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_UNPAID);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}