<!DOCTYPE html><html><head><meta charset="utf-8">
<style>body{font-family:Arial,sans-serif;background:#f5f5f5;}.container{max-width:600px;margin:30px auto;background:white;border-radius:12px;overflow:hidden;}.header{background:linear-gradient(135deg,#ff6b35,#f7971e);padding:30px;text-align:center;}.header h1{color:white;margin:0;font-size:22px;}.content{padding:30px;}.btn{display:inline-block;background:#6c3fd6;color:white;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:bold;}</style>
</head><body>
<div class="container">
<div class="header"><h1>🚀 New COD Order Confirmed!</h1></div>
<div class="content">
<p>A customer has confirmed a <strong>Cash on Delivery</strong> order.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0">
<tr><td style="padding:8px;background:#f8f9fa;font-weight:bold">Order</td><td style="padding:8px"><strong>{{ $order->order_number }}</strong></td></tr>
<tr><td style="padding:8px;background:#f8f9fa;font-weight:bold">Customer</td><td style="padding:8px">{{ $order->shipping_name }}</td></tr>
<tr><td style="padding:8px;background:#f8f9fa;font-weight:bold">Phone</td><td style="padding:8px">{{ $order->shipping_phone }}</td></tr>
<tr><td style="padding:8px;background:#f8f9fa;font-weight:bold">Address</td><td style="padding:8px">{{ $order->shipping_address }}</td></tr>
<tr><td style="padding:8px;background:#f8f9fa;font-weight:bold">Total</td><td style="padding:8px"><strong style="color:#6c3fd6;font-size:18px">${{ number_format($order->total, 2) }}</strong></td></tr>
<tr><td style="padding:8px;background:#f8f9fa;font-weight:bold">Payment</td><td style="padding:8px"><span style="background:#fff3cd;padding:3px 10px;border-radius:20px;font-weight:bold">Cash on Delivery</span></td></tr>
</table>
<div style="background:#d4edda;border-radius:8px;padding:16px;margin:16px 0">
<strong>⚠️ Action Required:</strong> Please prepare this order for delivery!
</div>
<div style="text-align:center;margin:24px 0">
<a href="{{ url('/admin/orders/'.$order->id) }}" class="btn">Manage Order →</a>
</div>
</div>
</div>
</body></html>
