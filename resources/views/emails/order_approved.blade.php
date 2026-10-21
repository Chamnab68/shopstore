<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Status Updated</title>
<style>
* { margin:0;padding:0;box-sizing:border-box; }
body { font-family:'Helvetica Neue',Arial,sans-serif;background:#f0f4f8;color:#333; }
.wrapper { max-width:600px;margin:30px auto;background:white;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08); }
.header { padding:36px 30px;text-align:center; }
.header.confirmed { background:linear-gradient(135deg,#17a2b8,#6610f2); }
.header.processing { background:linear-gradient(135deg,#6c3fd6,#a855f7); }
.header.shipped { background:linear-gradient(135deg,#fd7e14,#ffc107); }
.header.delivered { background:linear-gradient(135deg,#28a745,#20c997); }
.header.cancelled { background:linear-gradient(135deg,#dc3545,#c82333); }
.header h1 { color:white;font-size:22px;font-weight:700;margin-bottom:4px; }
.header p { color:rgba(255,255,255,.85);font-size:14px; }
.content { padding:30px; }
.status-big { text-align:center;padding:24px;margin-bottom:24px; }
.status-emoji { font-size:56px;margin-bottom:12px; }
.status-text { font-size:24px;font-weight:900; }
.order-info { background:#f8f5ff;border:2px solid #e8d5ff;border-radius:12px;padding:20px;margin-bottom:24px; }
.info-row { display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f0;font-size:14px; }
.info-row:last-child { border-bottom:none; }
.info-row .label { color:#888; }
.info-row .value { font-weight:700;color:#333; }
.items-table { width:100%;border-collapse:collapse;margin-bottom:20px; }
.items-table th { background:#f8f9fa;padding:10px 12px;text-align:left;font-size:12px;text-transform:uppercase;color:#888; }
.items-table td { padding:12px;border-bottom:1px solid #f0f0f0;font-size:14px; }
.note-box { border-radius:10px;padding:16px;margin-bottom:20px; }
.btn-wrap { text-align:center;margin:24px 0; }
.btn { display:inline-block;background:linear-gradient(135deg,#6c3fd6,#a855f7);color:white;padding:14px 36px;border-radius:50px;text-decoration:none;font-weight:700;font-size:15px; }
.footer { background:#f8f9fa;padding:20px 30px;text-align:center;border-top:1px solid #eee; }
.footer p { font-size:12px;color:#aaa;line-height:1.8; }
</style>
</head>
<body>
<div class="wrapper">
    @php
    $statusConfig = match($order->status) {
        'confirmed'  => ['class'=>'confirmed', 'emoji'=>'✅', 'title'=>'Order Confirmed!', 'sub'=>'Your order has been confirmed and is being prepared.', 'color'=>'#17a2b8'],
        'processing' => ['class'=>'processing','emoji'=>'⚙️', 'title'=>'Order Processing!','sub'=>'Your order is being processed.','color'=>'#6c3fd6'],
        'shipped'    => ['class'=>'shipped',   'emoji'=>'🚚','title'=>'Order Shipped!',   'sub'=>'Your order is on the way!','color'=>'#fd7e14'],
        'delivered'  => ['class'=>'delivered', 'emoji'=>'🎉','title'=>'Order Delivered!', 'sub'=>'Your order has been delivered successfully.','color'=>'#28a745'],
        'cancelled'  => ['class'=>'cancelled', 'emoji'=>'❌','title'=>'Order Cancelled',  'sub'=>'Your order has been cancelled.','color'=>'#dc3545'],
        default      => ['class'=>'confirmed', 'emoji'=>'🔄','title'=>'Order Updated',    'sub'=>'Your order status has been updated.','color'=>'#6c3fd6'],
    };
    @endphp

    <div class="header {{ $statusConfig['class'] }}">
        <div style="font-size:40px;margin-bottom:10px">{{ $statusConfig['emoji'] }}</div>
        <h1>{{ $statusConfig['title'] }}</h1>
        <p>{{ $statusConfig['sub'] }}</p>
    </div>

    <div class="content">
        <div class="order-info">
            <div class="info-row"><span class="label">📋 Order</span><span class="value">{{ $order->order_number }}</span></div>
            <div class="info-row"><span class="label">📊 Status</span><span class="value" style="color:{{ $statusConfig['color'] }}">{{ strtoupper($order->status) }}</span></div>
            <div class="info-row"><span class="label">💰 Total</span><span class="value" style="color:#6c3fd6">${{ number_format($order->total,2) }}</span></div>
            <div class="info-row"><span class="label">📍 Address</span><span class="value">{{ $order->shipping_address }}</span></div>
        </div>

        <table class="items-table">
            <thead><tr><th>Product</th><th style="text-align:center">Qty</th><th style="text-align:right">Price</th></tr></thead>
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

        @if($order->admin_notes)
        <div class="note-box" style="background:#f0f4f8;border-left:4px solid #6c3fd6">
            <div style="font-size:12px;color:#888;margin-bottom:6px">📝 NOTE FROM SHOPSTORE</div>
            <p style="font-size:14px;color:#333">{{ $order->admin_notes }}</p>
        </div>
        @endif

        @if($order->status === 'shipped')
        <div class="note-box" style="background:#fff3cd;border-left:4px solid #ffc107">
            <p style="font-size:14px;color:#856404">🚚 <strong>Your order is on the way!</strong><br>Our delivery team will contact you at <strong>{{ $order->shipping_phone }}</strong>.</p>
        </div>
        @elseif($order->status === 'delivered')
        <div class="note-box" style="background:#d4edda;border-left:4px solid #28a745">
            <p style="font-size:14px;color:#155724">🎉 <strong>Thank you for shopping with ShopStore!</strong><br>We hope you enjoy your purchase.</p>
        </div>
        @endif

        <div class="btn-wrap">
            <a href="{{ url('/orders/'.$order->id) }}" class="btn">View Order Details →</a>
        </div>
    </div>

    <div class="footer">
        <p>ShopStore — Your trusted grocery partner<br>© {{ date('Y') }} ShopStore. All rights reserved.</p>
    </div>
</div>
</body>
</html>
