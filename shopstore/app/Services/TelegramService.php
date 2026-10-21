<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    private string $token;
    private string $chatId;

    public function __construct()
    {
        $this->token  = env('TELEGRAM_BOT_TOKEN', '');
        $this->chatId = env('TELEGRAM_CHAT_ID', '');
    }

    public function send(string $message): bool
    {
        if (empty($this->token) || empty($this->chatId)) return false;

        try {
            $response = Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
                'chat_id'    => $this->chatId,
                'text'       => $message,
                'parse_mode' => 'HTML',
            ]);
            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('Telegram error: ' . $e->getMessage());
            return false;
        }
    }

    public function orderPlaced($order): bool
    {
        $msg = "🛒 <b>New Order Received!</b>\n\n"
             . "📦 Order: <b>{$order->order_number}</b>\n"
             . "👤 Customer: {$order->shipping_name}\n"
             . "📞 Phone: {$order->shipping_phone}\n"
             . "💰 Total: $" . number_format($order->total, 2) . "\n"
             . "💳 Payment: {$order->payment_method}\n"
             . "📍 Address: {$order->shipping_address}\n\n"
             . "🔗 View: " . url("/admin/orders/{$order->id}");
        return $this->send($msg);
    }

    public function receiptUploaded($order): bool
    {
        $msg = "📸 <b>Receipt Uploaded!</b>\n\n"
             . "📦 Order: <b>{$order->order_number}</b>\n"
             . "👤 Customer: {$order->shipping_name}\n"
             . "💰 Total: $" . number_format($order->total, 2) . "\n\n"
             . "✅ Please verify payment in admin panel.\n"
             . "🔗 " . url("/admin/orders/{$order->id}");
        return $this->send($msg);
    }

    public function orderStatusChanged($order): bool
    {
        $msg = "🔄 <b>Order Status Updated</b>\n\n"
             . "📦 Order: <b>{$order->order_number}</b>\n"
             . "📊 New Status: <b>" . strtoupper($order->status) . "</b>\n"
             . "👤 Customer: {$order->shipping_name}";
        return $this->send($msg);
    }
}
