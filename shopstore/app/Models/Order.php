<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number','user_id','status','payment_status','payment_method',
        'subtotal','discount','shipping_fee','total',
        'shipping_name','shipping_phone','shipping_address','notes',
        'receipt_image','receipt_uploaded_at','admin_notes',
        'approved_by','approved_at'
    ];

    protected $casts = [
        'receipt_uploaded_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_UNPAID = 'unpaid';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_REFUNDED = 'refunded';

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

    public static function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending'    => '<span class="badge bg-warning">Pending</span>',
            'confirmed'  => '<span class="badge bg-info">Confirmed</span>',
            'processing' => '<span class="badge bg-primary">Processing</span>',
            'shipped'    => '<span class="badge bg-secondary">Shipped</span>',
            'delivered'  => '<span class="badge bg-success">Delivered</span>',
            'cancelled'  => '<span class="badge bg-danger">Cancelled</span>',
            default      => '<span class="badge bg-light">Unknown</span>',
        };
    }

    public function getPaymentBadgeAttribute()
    {
        return match($this->payment_status) {
            'unpaid'   => '<span class="badge bg-danger">Unpaid</span>',
            'paid'     => '<span class="badge bg-success">Paid</span>',
            'refunded' => '<span class="badge bg-warning">Refunded</span>',
            default    => '<span class="badge bg-light">Unknown</span>',
        };
    }
}
