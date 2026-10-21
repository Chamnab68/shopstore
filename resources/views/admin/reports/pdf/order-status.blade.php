<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Status Report</title>
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
    <h2>📋 Order Status Report</h2>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th class="text-right">Orders Count</th>
                <th class="text-right">Total Value</th>
                <th class="text-right">Share</th>
            </tr>
        </thead>
        <tbody>
            @php $totalCount = $data->sum('count'); @endphp
            @forelse($data as $row)
            <tr>
                <td><strong>{{ ucfirst($row->status) }}</strong></td>
                <td class="text-right">{{ number_format($row->count) }}</td>
                <td class="text-right">${{ number_format($row->total, 2) }}</td>
                <td class="text-right">
                    @php $pct = $totalCount > 0 ? ($row->count / $totalCount) * 100 : 0; @endphp
                    {{ number_format($pct, 1) }}%
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center" style="color: #777; padding: 20px;">No order status data available.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>