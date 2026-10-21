<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmed</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #f0f4f8; color: #333; }
.wrapper { max-width: 600px; margin: 30px auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
.header { background: linear-gradient(135deg, #6c3fd6 0%, #a855f7 100%); padding: 40px 30px; text-align: center; }
.logo { font-size: 32px; margin-bottom: 8px; }
.header h1 { color: white; font-size: 22px; font-weight: 700; margin-bottom: 4px; }
.header p { color: rgba(255,255,255,.8); font-size: 14px; }
.badge { display: inline-block; background: rgba(255,255,255,.2); color: white; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; margin-top: 12px; }
.content { padding: 32px 30px; }
.greeting { font-size: 16px; color: #333; margin-bottom: 20px; }
.greeting strong { color: #6c3fd6; }
.order-box { background: #f8f5ff; border: 2px solid #e8d5ff; border-radius: 12px; padding: 20px; margin-bottom: 24px; text-align: center; }
.order-number { font-size: 28px; font-weight: 900; color: #6c3fd6; letter-spacing: 1px; }
.order-date { font-size: 13px; color: #888; margin-top: 4px; }
.section-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #888; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid #f0f0f0; }
.items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
.items-table th { background: #f8f9fa; padding: 10px 12px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; color: #888; }
.items-table td { padding: 12px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
.items-table tr:last-child td { border-bottom: none; }
.item-name { font-weight: 600; color: #333; }
.item-qty { color: #888; font-size: 13px; }
.price { font-weight: 700; color: #333; text-align: right; }
.totals { background: #f8f9fa; border-radius: 10px; padding: 16px; margin-bottom: 24px; }
.total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; color: #555; }
.total-row.final { border-top: 2px solid #e0e0e0; margin-top: 8px; padding-top: 12px; font-size: 18px; font-weight: 900; color: #6c3fd6; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
.info-card { background: #f8f9fa; border-radius: 10px; padding: 16px; }
.info-card-title { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #888; font-weight: 700; margin-bottom: 8px; }
.info-card p { font-size: 13px; color: #333; line-height: 1.6; }
.info-card strong { color: #333; }
.payment-badge { display: inline-block; background: #fff3cd; color: #856404; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
.payment-badge.cod { background: #d4edda; color: #155724; }
.bank-box { background: linear-gradient(135deg, #fff8e1, #fff3cd); border: 1px solid #ffc107; border-radius: 10px; padding: 16px; margin-bottom: 24px; }
.bank-box h4 { color: #856404; font-size: 14px; margin-bottom: 10px; }
.bank-box p { font-size: 13px; color: #555; line-height: 1.8; }
.bank-box .acct { font-size: 22px; font-weight: 900; color: #6c3fd6; letter-spacing: 2px; }
.btn-wrap { text-align: center; margin: 24px 0; }
.btn { display: inline-block; background: linear-gradient(135deg, #6c3fd6, #a855f7); color: white; padding: 14px 36px; border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 15px; letter-spacing: .3px; }
.footer { background: #f8f9fa; padding: 24px 30px; text-align: center; border-top: 1px solid #eee; }
.footer p { font-size: 12px; color: #aaa; line-height: 1.8; }
.footer a { color: #6c3fd6; text-decoration: none; }
.status-bar { display: flex; justify-content: space-around; margin: 20px 0; }
.step { text-align: center; flex: 1; position: relative; }
.step::after { content: ''; position: absolute; top: 16px; left: 60%; width: 80%; height: 2px; background: #e0e0e0; }
.step:last-child::after { display: none; }
.step-icon { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 6px; font-size: 14px; }
.step-icon.active { background: #6c3fd6; color: white; }
.step-icon.inactive { background: #e0e0e0; color: #aaa; }
.step-label { font-size: 11px; color: #888; }
.step-label.active { color: #6c3fd6; font-weight: 700; }
</style>
</head>
<body>
<div class="wrapper">
    <!-- Header -->
    <div class="header">
        <div class="logo">🛒</div>
        <h1>Order Confirmed!</h1>
        <p>Thank you for shopping with ShopStore</p>
        <div class="badge">✅ Order Received</div>
    </div>

    <div class="content">
        <!-- Greeting -->
        <p class="greeting">Hi <strong>{{ $order->user->name ?? $order->shipping_name }}</strong>,</p>
        <p style="font-size:14px;color:#555;margin-bottom:24px;line-height:1.6">
            We've received your order and it's being processed. You'll receive another update when it ships.
        </p>

        <!-- Order Number -->
        <div class="order-box">
            <div style="font-size:12px;color:#888;margin-bottom:4px">ORDER NUMBER</div>
            <div class="order-number">{{ $order->order_number }}</div>
            <div class="order-date">Placed on {{ $order->created_at->format('d M Y, H:i') }}</div>
        </div>

        <!-- Order Status Steps -->
        <div class="status-bar">
            <div class="step">
                <div class="step-icon active">✓</div>
                <div class="step-label active">Confirmed</div>
            </div>
            <div class="step">
                <div class="step-icon inactive">⚙</div>
                <div class="step-label">Processing</div>
            </div>
            <div class="step">
                <div class="step-icon inactive">🚚</div>
                <div class="step-label">Shipped</div>
            </div>
            <div class="step">
                <div class="step-icon inactive">🎉</div>
                <div class="step-label">Delivered</div>
            </div>
        </div>

        <!-- Items -->
        <div class="section-title">Order Items</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align:center">Qty</th>
                    <th style="text-align:right">Price</th>
                    <th style="text-align:right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
            @foreach($order->items as $item)
            <tr>
                <td><div class="item-name">{{ $item->product_name }}</div></td>
                <td style="text-align:center"><span class="item-qty">× {{ $item->quantity }}</span></td>
                <td class="price">${{ number_format($item->sale_price ?? $item->price, 2) }}</td>
                <td class="price">${{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal</span>
                <span>${{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="total-row">
                <span>Shipping</span>
                <span>{{ $order->shipping_fee > 0 ? '$'.number_format($order->shipping_fee,2) : '🎉 FREE' }}</span>
            </div>
            @if($order->discount > 0)
            <div class="total-row" style="color:#28a745">
                <span>Discount</span>
                <span>-${{ number_format($order->discount, 2) }}</span>
            </div>
            @endif
            <div class="total-row final">
                <span>Total</span>
                <span>${{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-title">📦 Shipping Address</div>
                <p>
                    <strong>{{ $order->shipping_name }}</strong><br>
                    {{ $order->shipping_address }}<br>
                    📞 {{ $order->shipping_phone }}
                </p>
            </div>
            <div class="info-card">
                <div class="info-card-title">💳 Payment</div>
                <p>
                    <strong>Method:</strong>
                    @if($order->payment_method === 'cash_on_delivery')
                    <span class="payment-badge cod">Cash on Delivery</span>
                    @else
                    <span class="payment-badge">Bank Transfer / QR</span>
                    @endif
                    <br>
                    <strong>Date:</strong> {{ $order->created_at->format('d M Y, H:i') }}<br>
                    <strong>Amount:</strong> <span style="color:#6c3fd6;font-weight:700">${{ number_format($order->total,2) }}</span>
                </p>
            </div>
        </div>

        @if($order->payment_method === 'bank_transfer')
        <!-- Bank Info -->
        <div class="bank-box">
            <h4>💳 Payment Instructions</h4>
            <p>
                Please transfer <strong>${{ number_format($order->total, 2) }}</strong> to:<br>
                Bank: <strong>{{ env('BANK_NAME','ACLEDA Bank') }}</strong><br>
                Account Name: <strong>{{ env('BANK_ACCOUNT_NAME','ShopStore') }}</strong><br>
                Account Number: <span class="acct">{{ env('BANK_ACCOUNT_NUMBER','000123456') }}</span><br>
                Reference: <strong>{{ $order->order_number }}</strong>
            </p>
        </div>
        @endif

        @if($order->notes)
        <div style="background:#f0f4f8;border-radius:10px;padding:14px;margin-bottom:20px">
            <div style="font-size:12px;color:#888;margin-bottom:4px">📝 ORDER NOTES</div>
            <p style="font-size:13px;color:#555">{{ $order->notes }}</p>
        </div>
        @endif

        <!-- CTA Button -->
        <div class="btn-wrap">
            <a href="{{ url('/orders/'.$order->id) }}" class="btn">Track My Order →</a>
        </div>

        <p style="font-size:13px;color:#888;text-align:center;margin-top:16px">
            Questions about your order? Contact us at<br>
            <a href="mailto:{{ env('MAIL_FROM_ADDRESS') }}" style="color:#6c3fd6">{{ env('MAIL_FROM_ADDRESS') }}</a>
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            <strong style="color:#333">ShopStore</strong> — Your trusted grocery partner<br>
            © {{ date('Y') }} ShopStore. All rights reserved.<br>
            <a href="{{ url('/') }}">Visit Store</a> ·
            <a href="{{ url('/orders') }}">My Orders</a>
        </p>
    </div>
</div>
</body>
</html>
