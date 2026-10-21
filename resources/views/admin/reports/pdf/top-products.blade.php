<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Top Products Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>⭐ Top Products Report</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">#</th>
                <th>Product Name</th>
                <th>Category</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total Sold</th>
                <th class="text-right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $p)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $p->name }}</strong><br>
                    <small style="color: #666;">SKU: {{ $p->sku ?? 'N/A' }}</small>
                </td>
                <td>{{ $p->category->name ?? '—' }}</td>
                <td class="text-right">${{ number_format($p->sale_price ?? $p->price, 2) }}</td>
                <td class="text-right">{{ number_format($p->total_sold ?? 0) }}</td>
                <td class="text-right" style="color: #28a745; font-weight: bold;">
                    ${{ number_format($p->revenue ?? 0, 2) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px; color: #777;">No sales data available.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>