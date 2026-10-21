<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receipt Uploaded</title>
<style>
* { margin:0;padding:0;box-sizing:border-box; }
body { font-family:'Helvetica Neue',Arial,sans-serif;background:#f0f4f8;color:#333; }
.wrapper { max-width:600px;margin:30px auto;background:white;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08); }
.header { background:linear-gradient(135deg,#28a745,#20c997);padding:36px 30px;text-align:center; }
.header h1 { color:white;font-size:22px;font-weight:700;margin-bottom:4px; }
.header p { color:rgba(255,255,255,.85);font-size:14px; }
.content { padding:30px; }
.alert-box { background:#d4edda;border:1px solid #c3e6cb;border-radius:10px;padding:16px;margin-bottom:24px;text-align:center; }
.alert-box p { color:#155724;font-size:14px;font-weight:600; }
.order-info { background:#f8f5ff;border:2px solid #e8d5ff;border-radius:12px;padding:20px;margin-bottom:24px; }
.order-info-row { display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f0;font-size:14px; }
.order-info-row:last-child { border-bottom:none;padding-top:12px;margin-top:4px; }
.order-info-row .label { color:#888; }
.order-info-row .value { font-weight:700;color:#333; }
.order-info-row.total .value { font-size:20px;color:#6c3fd6; }
.items-table { width:100%;border-collapse:collapse;margin-bottom:20px; }
.items-table th { background:#f8f9fa;padding:10px 12px;text-align:left;font-size:12px;text-transform:uppercase;color:#888; }
.items-table td { padding:12px;border-bottom:1px solid #f0f0f0;font-size:14px; }
.action-box { background:#fff3cd;border:1px solid #ffc107;border-radius:10px;padding:16px;margin-bottom:20px; }
.action-box h4 { color:#856404;font-size:14px;margin-bottom:8px; }
.action-box p { color:#555;font-size:13px;line-height:1.6; }
.btn-wrap { text-align:center;margin:24px 0; }
.btn { display:inline-block;background:linear-gradient(135deg,#6c3fd6,#a855f7);color:white;padding:14px 36px;border-radius:50px;text-decoration:none;font-weight:700;font-size:15px; }
.footer { background:#f8f9fa;padding:20px 30px;text-align:center;border-top:1px solid #eee; }
.footer p { font-size:12px;color:#aaa;line-height:1.8; }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div style="font-size:40px;margin-bottom:10px">📸</div>
        <h1>Payment Receipt Uploaded!</h1>
        <p>A customer has submitted payment for verification</p>
    </div>
    <div class="content">
        <div class="alert-box">
            <p>✅ Receipt has been uploaded — Please verify and approve the order!</p>
        </div>

        <!-- Order Info -->
        <div class="order-info">
            <div class="order-info-row">
                <span class="label">📋 Order Number</span>
                <span class="value">{{ $order->order_number }}</span>
            </div>
            <div class="order-info-row">
                <span class="label">👤 Customer</span>
                <span class="value">{{ $order->shipping_name }}</span>
            </div>
            <div class="order-info-row">
                <span class="label">📧 Email</span>
                <span class="value">{{ $order->user->email ?? 'N/A' }}</span>
            </div>
            <div class="order-info-row">
                <span class="label">📞 Phone</span>
                <span class="value">{{ $order->shipping_phone }}</span>
            </div>
            <div class="order-info-row">
                <span class="label">📍 Address</span>
                <span class="value">{{ $order->shipping_address }}</span>
            </div>
            <div class="order-info-row">
                <span class="label">⏰ Uploaded At</span>
                <span class="value">{{ $order->receipt_uploaded_at?->format('d M Y, H:i') }}</span>
            </div>
            <div class="order-info-row total">
                <span class="label">💰 Total Amount</span>
                <span class="value">${{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <!-- Items -->
        <table class="items-table">
            <thead><tr><th>Product</th><th style="text-align:center">Qty</th><th style="text-align:right">Subtotal</th></tr></thead>
            <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td style="text-align:center">× {{ $item->quantity }}</td>
                <td style="text-align:right;font-weight:700">${{ number_format($item->subtotal,2) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>

        <div class="action-box">
            <h4>⚠️ Action Required</h4>
            <p>Please log in to the admin panel to verify the payment receipt and approve this order.</p>
        </div>

        <div class="btn-wrap">
            <a href="{{ url('/admin/orders/'.$order->id) }}" class="btn">Verify & Approve Order →</a>
        </div>
    </div>
    <div class="footer">
        <p>ShopStore Admin Notification<br>© {{ date('Y') }} ShopStore. All rights reserved.</p>
    </div>
</div>
</body>
</html>
