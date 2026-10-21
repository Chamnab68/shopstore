<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
.container { max-width: 600px; margin: 30px auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
.header { background: linear-gradient(135deg, #6c3fd6, #a855f7); padding: 35px; text-align: center; }
.header h1 { color: white; margin: 0 0 8px; font-size: 26px; }
.header p { color: rgba(255,255,255,.8); margin: 0; font-size: 14px; }
.content { padding: 30px; }
.stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 20px 0; }
.stat-card { background: #f8f9fa; border-radius: 12px; padding: 20px; text-align: center; border-left: 4px solid #6c3fd6; }
.stat-card.green { border-left-color: #28a745; }
.stat-card.orange { border-left-color: #ff6b35; }
.stat-card.red { border-left-color: #dc3545; }
.stat-value { font-size: 28px; font-weight: 900; color: #1a1a2e; }
.stat-label { font-size: 12px; color: #888; margin-top: 4px; text-transform: uppercase; letter-spacing: 1px; }
.revenue-box { background: linear-gradient(135deg, #6c3fd6, #a855f7); border-radius: 14px; padding: 24px; color: white; text-align: center; margin: 20px 0; }
.revenue-box .amount { font-size: 42px; font-weight: 900; }
.revenue-box .label { opacity: .85; font-size: 14px; margin-top: 4px; }
table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 14px; }
th { background: #f8f9fa; padding: 10px 14px; text-align: left; color: #888; font-size: 12px; text-transform: uppercase; }
td { padding: 10px 14px; border-top: 1px solid #eee; }
.badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.badge-success { background: #d4edda; color: #155724; }
.badge-danger { background: #f8d7da; color: #721c24; }
.btn { display: inline-block; background: #6c3fd6; color: white; padding: 14px 32px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 15px; }
.footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #888; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <div style="font-size:48px;margin-bottom:12px">📊</div>
        <h1>Monthly Sales Report</h1>
        <p>{{ $month }} — ShopStore Analytics</p>
    </div>

    <div class="content">
        {{-- Revenue Highlight --}}
        <div class="revenue-box">
            <div class="amount">${{ number_format($total_revenue, 2) }}</div>
            <div class="label">Total Revenue for {{ $month }}</div>
            <div style="margin-top:12px;font-size:13px;opacity:.8">
                Paid: <strong>${{ number_format($paid_revenue, 2) }}</strong>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $total_orders }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card green">
                <div class="stat-value" style="color:#28a745">{{ $completed_orders }}</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-value" style="color:#ff6b35">{{ $new_customers }}</div>
                <div class="stat-label">New Customers</div>
            </div>
            <div class="stat-card red">
                <div class="stat-value" style="color:#dc3545">{{ $cancelled_orders }}</div>
                <div class="stat-label">Cancelled</div>
            </div>
        </div>

        {{-- Details Table --}}
        <table>
            <tr><th>Metric</th><th>Value</th></tr>
            <tr><td>Total Orders</td><td><strong>{{ $total_orders }}</strong></td></tr>
            <tr><td>Delivered Orders</td><td><span class="badge badge-success">{{ $completed_orders }}</span></td></tr>
            <tr><td>Cancelled Orders</td><td><span class="badge badge-danger">{{ $cancelled_orders }}</span></td></tr>
            <tr><td>Pending Orders</td><td>{{ $pending_orders }}</td></tr>
            <tr><td>Total Revenue</td><td><strong style="color:#6c3fd6">${{ number_format($total_revenue, 2) }}</strong></td></tr>
            <tr><td>Paid Revenue</td><td><strong style="color:#28a745">${{ number_format($paid_revenue, 2) }}</strong></td></tr>
            <tr><td>New Customers</td><td>{{ $new_customers }}</td></tr>
            <tr><td>Products Sold</td><td>{{ $products_sold }} units</td></tr>
            <tr><td>🏆 Top Product</td><td><strong>{{ $top_product }}</strong></td></tr>
        </table>

        <div style="text-align:center;margin:28px 0">
            <a href="{{ url('/admin/reports/sales') }}" class="btn">View Full Report →</a>
        </div>
    </div>

    <div class="footer">
        <p>ShopStore — Automated Monthly Report</p>
        <p>{{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}</p>
        <p>© {{ date('Y') }} ShopStore. All rights reserved.</p>
    </div>
</div>
</body>
</html>
