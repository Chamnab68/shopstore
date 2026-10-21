<!DOCTYPE html><html><head><meta charset="utf-8">
<style>body{font-family:Arial,sans-serif;background:#f5f5f5;}.container{max-width:600px;margin:30px auto;background:white;border-radius:12px;overflow:hidden;}.header{background:linear-gradient(135deg,#28a745,#20c997);padding:30px;text-align:center;}.header h1{color:white;margin:0;font-size:22px;}.content{padding:30px;}.btn{display:inline-block;background:#6c3fd6;color:white;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:bold;}</style>
</head><body>
<div class="container">
<div class="header"><h1>✅ Order Confirmed!</h1></div>
<div class="content">
<p>Dear <strong>{{ $order->user->name ?? $order->shipping_name }}</strong>,</p>
<p>Your Cash on Delivery order has been confirmed! Our team will contact you soon for delivery.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0">
<tr><td style="padding:8px;background:#f8f9fa;font-weight:bold">Order</td><td style="padding:8px"><strong>{{ $order->order_number }}</strong></td></tr>
<tr><td style="padding:8px;background:#f8f9fa;font-weight:bold">Total</td><td style="padding:8px"><strong style="color:#28a745;font-size:18px">${{ number_format($order->total, 2) }}</strong></td></tr>
<tr><td style="padding:8px;background:#f8f9fa;font-weight:bold">Delivery To</td><td style="padding:8px">{{ $order->shipping_address }}</td></tr>
<tr><td style="padding:8px;background:#f8f9fa;font-weight:bold">Contact</td><td style="padding:8px">{{ $order->shipping_phone }}</td></tr>
</table>
<div style="background:#d4edda;border-radius:8px;padding:16px;margin:16px 0;font-size:14px">
🚚 <strong>Please prepare:</strong> ${{ number_format($order->total, 2) }} cash for the delivery person.
</div>
<div style="text-align:center;margin:24px 0">
<a href="{{ url('/orders/'.$order->id) }}" class="btn">Track My Order →</a>
</div>
</div>
</div>
</body></html>
