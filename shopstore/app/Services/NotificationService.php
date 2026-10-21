<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function __construct(private TelegramService $telegram) {}

    public function orderPlaced(Order $order): void
    {
        // Telegram
        $this->telegram->orderPlaced($order);

        // Gmail
        try {
            Mail::send('emails.order_placed', ['order' => $order], function ($m) use ($order) {
                $m->to($order->user->email, $order->user->name)
                  ->subject("Order Confirmed: {$order->order_number}");
            });
        } catch (\Exception $e) {
            \Log::error('Email failed: ' . $e->getMessage());
        }
    }

    public function receiptUploaded(Order $order): void
    {
        $this->telegram->receiptUploaded($order);

        try {
            // Notify admin
            $adminEmail = config('mail.admin_email', env('MAIL_FROM_ADDRESS'));
            Mail::send('emails.receipt_uploaded', ['order' => $order], function ($m) use ($order, $adminEmail) {
                $m->to($adminEmail, 'Admin')
                  ->subject("Receipt Uploaded - {$order->order_number}");
            });
        } catch (\Exception $e) {
            \Log::error('Email failed: ' . $e->getMessage());
        }
    }

    public function orderApproved(Order $order): void
    {
        $this->telegram->orderStatusChanged($order);

        try {
            Mail::send('emails.order_approved', ['order' => $order], function ($m) use ($order) {
                $m->to($order->user->email, $order->user->name)
                  ->subject("Your Order Has Been Approved: {$order->order_number}");
            });
        } catch (\Exception $e) {
            \Log::error('Email failed: ' . $e->getMessage());
        }
    }
}
