{{-- resources/views/emails/receipt_uploaded.blade.php --}}
<!DOCTYPE html><html><head><meta charset="utf-8">
<style>body{font-family:Arial,sans-serif;background:#f5f5f5;}.container{max-width:600px;margin:30px auto;background:white;border-radius:12px;overflow:hidden;}.header{background:linear-gradient(135deg,#28a745,#20c997);padding:30px;text-align:center;}.header h1{color:white;margin:0;font-size:22px;}.content{padding:30px;}.btn{display:inline-block;background:#6c3fd6;color:white;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:bold;}</style>
</head><body>
<div class="container">
<div class="header"><h1>📸 Payment Receipt Uploaded</h1></div>
<div class="content">
<p>A customer has uploaded a payment receipt for order <strong>{{ $order->order_number }}</strong>.</p>
<table style="width:100%;border-collapse:collapse;margin:20px 0">
<tr><td style="padding:8px;background:#f8f9fa;font-weight:bold">Order</td><td style="padding:8px">{{ $order->order_number }}</td></tr>
<tr><td style="padding:8px;background:#f8f9fa;font-weight:bold">Customer</td><td style="padding:8px">{{ $order->shipping_name }}</td></tr>
<tr><td style="padding:8px;background:#f8f9fa;font-weight:bold">Total</td><td style="padding:8px"><strong style="color:#6c3fd6">${{ number_format($order->total,2) }}</strong></td></tr>
<tr><td style="padding:8px;background:#f8f9fa;font-weight:bold">Uploaded At</td><td style="padding:8px">{{ $order->receipt_uploaded_at->format('d M Y H:i') }}</td></tr>
</table>
<p>Please verify the payment and approve the order in the admin panel.</p>
<div style="text-align:center;margin:25px 0"><a href="{{ url('/admin/orders/'.$order->id) }}" class="btn">View Order & Approve</a></div>
</div>
</div>
</body></html>
