<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function __construct(private TelegramService $telegram) {}

    // Bank Transfer — order placed (NO telegram, wait for receipt)
    public function orderPlaced(Order $order): void
    {
        // Only send email to customer, NOT telegram
        // Telegram will fire when receipt is uploaded
        $this->sendMail('emails.order_placed', $order, $order->user->email,
            "✅ Order Confirmed: {$order->order_number}");
    }

    // Customer uploads receipt + clicks Confirm Payment → Telegram + Gmail
    public function receiptUploaded(Order $order): void
    {
        // Load items for message
        $order->load('items', 'user');

        // Telegram to admin
        $this->telegram->receiptUploaded($order);

        // Email to admin
        $adminEmail = env('MAIL_FROM_ADDRESS', 'admin@shopstore.com');
        $this->sendMail('emails.receipt_uploaded', $order, $adminEmail,
            "📸 Payment Receipt - {$order->order_number}");
    }

    // COD — customer clicks Process Order → Telegram + Gmail
    public function orderProcessed(Order $order): void
    {
        $order->load('items', 'user');

        // Telegram to admin
        $this->telegram->orderProcessed($order);

        // Email to admin
        $adminEmail = env('MAIL_FROM_ADDRESS', 'admin@shopstore.com');
        $this->sendMail('emails.order_processed_admin', $order, $adminEmail,
            "🚀 COD Order Confirmed - {$order->order_number}");

        // Email to customer
        if ($order->user) {
            $this->sendMail('emails.order_processed_customer', $order, $order->user->email,
                "✅ Your Order Confirmed: {$order->order_number}");
        }
    }

    // Admin approves/changes status → Telegram + Gmail to customer
    public function orderApproved(Order $order): void
    {
        $this->telegram->orderStatusChanged($order);

        if ($order->user) {
            $this->sendMail('emails.order_approved', $order, $order->user->email,
                "🔄 Order Updated: {$order->order_number}");
        }
    }

    // Monthly summary
    public function monthlySummary(array $data): void
    {
        $this->telegram->monthlySummary($data);

        $adminEmail = env('MAIL_FROM_ADDRESS', 'admin@shopstore.com');
        $this->sendMailData('emails.monthly_summary', $data, $adminEmail,
            "📊 Monthly Sales Report - " . $data['month']);
    }

    // ── Helpers ───────────────────────────────────────────
    private function sendMail(string $view, Order $order, string $to, string $subject): void
    {
        try {
            Mail::send($view, ['order' => $order], function ($m) use ($to, $subject) {
                $m->to($to)->subject($subject);
            });
        } catch (\Exception $e) {
            \Log::error("Mail failed [{$subject}]: " . $e->getMessage());
        }
    }

    private function sendMailData(string $view, array $data, string $to, string $subject): void
    {
        try {
            Mail::send($view, $data, function ($m) use ($to, $subject) {
                $m->to($to)->subject($subject);
            });
        } catch (\Exception $e) {
            \Log::error("Mail failed [{$subject}]: " . $e->getMessage());
        }
    }
}
