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
            $response = Http::withoutVerifying()->post(
                "https://api.telegram.org/bot{$this->token}/sendMessage",
                [
                    'chat_id'    => $this->chatId,
                    'text'       => $message,
                    'parse_mode' => 'HTML',
                ]
            );
            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('Telegram error: ' . $e->getMessage());
            return false;
        }
    }

    // ── ផ្ញើពេល customer upload receipt & confirm payment ──
    public function receiptUploaded($order): bool
    {
        $items = '';
        foreach ($order->items as $item) {
            $price  = $item->sale_price ?? $item->price;
            $sub    = number_format($price * $item->quantity, 2);
            $items .= "  • {$item->product_name} x{$item->quantity} = \${$sub}\n";
        }

        $paymentMethod = match($order->payment_method) {
            'bank_transfer'    => 'Bank Transfer / QR',
            'cash_on_delivery' => 'Cash on Delivery',
            default            => $order->payment_method,
        };

        $time = $order->receipt_uploaded_at
            ? $order->receipt_uploaded_at->format('d/m/Y H:i')
            : now()->format('d/m/Y H:i');

        $msg = "🛒 <b>NEW ORDER PAYMENT CONFIRMED!</b>\n"
             . "━━━━━━━━━━━━━━━━━━\n"
             . "📋 Order: <b>{$order->order_number}</b>\n"
             . "👤 Customer: <b>{$order->shipping_name}</b>\n"
             . "📧 Email: " . ($order->user->email ?? 'N/A') . "\n"
             . "📞 Phone: {$order->shipping_phone}\n"
             . "📍 Address: {$order->shipping_address}\n"
             . "━━━━━━━━━━━━━━━━━━\n"
             . "🛍️ <b>Items:</b>\n"
             . $items
             . "━━━━━━━━━━━━━━━━━━\n"
             . "💳 Payment: {$paymentMethod}\n"
             . "💰 Total: <b>\${$order->total}</b>\n"
             . "⏰ Time: {$time}\n"
             . "━━━━━━━━━━━━━━━━━━\n"
             . "📸 Receipt uploaded — Please verify!\n"
             . "✅ Status: <b>" . strtoupper($order->status) . "</b>\n"
             . "🔗 " . url("/admin/orders/{$order->id}");

        return $this->send($msg);
    }

    // ── ផ្ញើពេល COD customer ចុច Process Order ──
    public function orderProcessed($order): bool
    {
        $items = '';
        foreach ($order->items as $item) {
            $price  = $item->sale_price ?? $item->price;
            $sub    = number_format($price * $item->quantity, 2);
            $items .= "  • {$item->product_name} x{$item->quantity} = \${$sub}\n";
        }

        $time = now()->format('d/m/Y H:i');

        $msg = "🚀 <b>NEW COD ORDER CONFIRMED!</b>\n"
             . "━━━━━━━━━━━━━━━━━━\n"
             . "📋 Order: <b>{$order->order_number}</b>\n"
             . "👤 Customer: <b>{$order->shipping_name}</b>\n"
             . "📧 Email: " . ($order->user->email ?? 'N/A') . "\n"
             . "📞 Phone: {$order->shipping_phone}\n"
             . "📍 Address: {$order->shipping_address}\n"
             . "━━━━━━━━━━━━━━━━━━\n"
             . "🛍️ <b>Items:</b>\n"
             . $items
             . "━━━━━━━━━━━━━━━━━━\n"
             . "💳 Payment: Cash on Delivery\n"
             . "💰 Total: <b>\${$order->total}</b>\n"
             . "⏰ Time: {$time}\n"
             . "━━━━━━━━━━━━━━━━━━\n"
             . "⚠️ Please prepare for delivery!\n"
             . "✅ Status: <b>CONFIRMED</b>\n"
             . "🔗 " . url("/admin/orders/{$order->id}");

        return $this->send($msg);
    }

    // ── Order Status Changed (Admin approve) ──
    public function orderStatusChanged($order): bool
    {
        $statusEmoji = match($order->status) {
            'confirmed'  => '✅',
            'processing' => '⚙️',
            'shipped'    => '🚚',
            'delivered'  => '🎉',
            'cancelled'  => '❌',
            default      => '🔄',
        };

        $msg = "{$statusEmoji} <b>Order Status Updated</b>\n"
             . "━━━━━━━━━━━━━━━━━━\n"
             . "📋 Order: <b>{$order->order_number}</b>\n"
             . "👤 Customer: {$order->shipping_name}\n"
             . "📊 Status: <b>" . strtoupper($order->status) . "</b>\n"
             . "💰 Total: \${$order->total}\n"
             . "━━━━━━━━━━━━━━━━━━\n"
             . "🔗 " . url("/admin/orders/{$order->id}");

        return $this->send($msg);
    }

    // ── Monthly Summary ──
    public function monthlySummary(array $data): bool
    {
        $msg = "📊 <b>MONTHLY SALES REPORT</b>\n"
             . "📅 Month: <b>{$data['month']}</b>\n"
             . "━━━━━━━━━━━━━━━━━━\n"
             . "🛒 Total Orders: <b>{$data['total_orders']}</b>\n"
             . "✅ Completed: <b>{$data['completed_orders']}</b>\n"
             . "❌ Cancelled: <b>{$data['cancelled_orders']}</b>\n"
             . "⏳ Pending: <b>{$data['pending_orders']}</b>\n"
             . "━━━━━━━━━━━━━━━━━━\n"
             . "💰 Total Revenue: <b>\$" . number_format($data['total_revenue'], 2) . "</b>\n"
             . "💵 Paid Revenue: <b>\$" . number_format($data['paid_revenue'], 2) . "</b>\n"
             . "👥 New Customers: <b>{$data['new_customers']}</b>\n"
             . "📦 Products Sold: <b>{$data['products_sold']}</b>\n"
             . "━━━━━━━━━━━━━━━━━━\n"
             . "🏆 Top Product: <b>{$data['top_product']}</b>\n"
             . "🔗 " . url("/admin/reports/sales");

        return $this->send($msg);
    }
}
