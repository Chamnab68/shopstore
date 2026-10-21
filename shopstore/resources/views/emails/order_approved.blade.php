<!DOCTYPE html><html><head><meta charset="utf-8">
<style>body{font-family:Arial,sans-serif;background:#f5f5f5;}.container{max-width:600px;margin:30px auto;background:white;border-radius:12px;overflow:hidden;}.header{background:linear-gradient(135deg,#6c3fd6,#a855f7);padding:30px;text-align:center;}.header h1{color:white;margin:0;}.content{padding:30px;}.btn{display:inline-block;background:#6c3fd6;color:white;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:bold;}.status{display:inline-block;padding:6px 16px;border-radius:20px;font-weight:bold;font-size:16px;}</style>
</head><body>
<div class="container">
<div class="header"><h1>✅ Your Order Has Been Updated</h1></div>
<div class="content">
<p>Dear <strong>{{ $order->user->name }}</strong>,</p>
<p>Your order status has been updated:</p>
<div style="text-align:center;margin:20px 0">
<div style="font-size:14px;color:#888;margin-bottom:8px">Order: <strong>{{ $order->order_number }}</strong></div>
<span class="status" style="background:{{ ['confirmed'=>'#17a2b8','processing'=>'#6c3fd6','shipped'=>'#6c757d','delivered'=>'#28a745','cancelled'=>'#dc3545'][$order->status] ?? '#6c757d' }};color:white">
{{ strtoupper($order->status) }}
</span>
</div>
@if($order->admin_notes)
<div style="background:#f8f9fa;border-radius:8px;padding:16px;margin:20px 0">
<strong>Note from our team:</strong><br>{{ $order->admin_notes }}
</div>
@endif
@if($order->status === 'shipped')
<div style="background:#d4edda;border-radius:8px;padding:16px;margin:20px 0">
<strong>🚚 Your order is on its way!</strong><br>
Our delivery team will contact you at {{ $order->shipping_phone }}.
</div>
@elseif($order->status === 'delivered')
<div style="background:#d4edda;border-radius:8px;padding:16px;margin:20px 0">
<strong>🎉 Order Delivered!</strong><br>
Thank you for shopping with ShopStore!
</div>
@endif
<div style="text-align:center;margin:25px 0"><a href="{{ url('/orders/'.$order->id) }}" class="btn">View Order Details</a></div>
</div>
</div>
</body></html>
