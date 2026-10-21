<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>💳 Payment Report</h2>
    <table>
        <thead>
            <tr>
                <th>Payment Method</th>
                <th>Payment Status</th>
                <th class="text-right">Count</th>
                <th class="text-right">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                <td><strong>{{ ucwords(str_replace('_', ' ', $row->payment_method)) }}</strong></td>
                <td>{{ ucfirst($row->payment_status) }}</td>
                <td class="text-right">{{ number_format($row->count) }}</td>
                <td class="text-right">${{ number_format($row->total, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center" style="color: #777; padding: 20px;">No payment data available.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="2">TOTAL</td>
                <td class="text-right">{{ number_format($data->sum('count')) }}</td>
                <td class="text-right">${{ number_format($data->sum('total'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>