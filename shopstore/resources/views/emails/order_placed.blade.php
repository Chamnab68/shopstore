{{-- resources/views/emails/order_placed.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
        .header { background: linear-gradient(135deg, #6c3fd6, #a855f7); padding: 30px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .header p { color: rgba(255,255,255,.8); margin: 5px 0 0; }
        .content { padding: 30px; }
        .order-box { background: #f8f9fa; border-radius: 10px; padding: 20px; margin: 20px 0; }
        .order-number { font-size: 22px; font-weight: bold; color: #6c3fd6; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 10px; text-align: left; font-size: 13px; }
        td { padding: 10px; border-bottom: 1px solid #eee; font-size: 13px; }
        .total-row { font-weight: bold; font-size: 16px; color: #6c3fd6; }
        .btn { display: inline-block; background: #6c3fd6; color: white; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: bold; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #888; }
        .bank-box { background: #fff3cd; border-radius: 8px; padding: 15px; margin: 15px 0; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🛒 ShopStore</h1>
        <p>Order Confirmed!</p>
    </div>
    <div class="content">
        <p>Dear <strong>{{ $order->user->name }}</strong>,</p>
        <p>Thank you for your order! We've received it and will process it shortly.</p>

        <div class="order-box">
            <div class="order-number">{{ $order->order_number }}</div>
            <div style="font-size:13px;color:#888;margin-top:4px">Placed on {{ $order->created_at->format('d M Y H:i') }}</div>
        </div>

        <h3>Order Items</h3>
        <table>
            <thead><tr><th>Product</th><th>Qty</th><th>Price</th></tr></thead>
            <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->subtotal,2) }}</td>
            </tr>
            @endforeach
            <tr><td colspan="2" style="text-align:right">Shipping</td><td>${{ number_format($order->shipping_fee,2) }}</td></tr>
            <tr class="total-row"><td colspan="2" style="text-align:right">TOTAL</td><td>${{ number_format($order->total,2) }}</td></tr>
            </tbody>
        </table>

        <h3>Shipping Address</h3>
        <p>{{ $order->shipping_name }}<br>{{ $order->shipping_phone }}<br>{{ $order->shipping_address }}</p>

        @if($order->payment_method === 'bank_transfer')
        <div class="bank-box">
            <h4 style="margin:0 0 10px">💳 Payment Instructions</h4>
            <p style="margin:0">Please transfer <strong>${{ number_format($order->total,2) }}</strong> to:</p>
            <p style="margin:5px 0"><strong>{{ env('BANK_NAME','ABA Bank') }}</strong><br>
            Account: <strong>{{ env('BANK_ACCOUNT_NUMBER','000123456') }}</strong><br>
            Name: <strong>{{ env('BANK_ACCOUNT_NAME','ShopStore') }}</strong></p>
            <p style="margin:5px 0;font-size:12px">Reference: <strong>{{ $order->order_number }}</strong></p>
        </div>
        @endif

        <div style="text-align:center;margin-top:25px">
            <a href="{{ url('/orders/'.$order->id) }}" class="btn">View Order Details</a>
        </div>
    </div>
    <div class="footer">
        <p>ShopStore — Your trusted grocery partner</p>
        <p>© {{ date('Y') }} ShopStore. All rights reserved.</p>
    </div>
</div>
</body>
</html>
