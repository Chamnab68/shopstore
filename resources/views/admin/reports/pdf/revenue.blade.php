<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    @php
        // ពិនិត្យមើលថាវាជាប្រភេទ Revenue by Category ឬក៏អត់ ផ្អែកលើទិន្នន័យដែលមាន field 'qty' ឬ 'revenue'
        $firstItem = $data->first();
        $isCategoryReport = $firstItem && (isset($firstItem->qty) || isset($firstItem->revenue) && !isset($firstItem->order_id));
    @endphp

    @if($isCategoryReport)
        {{-- 📋 ទម្រង់សម្រាប់ Revenue by Category --}}
        <h2>Revenue by Category Report</h2>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-right">Qty Sold</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Share</th>
                </tr>
            </thead>
            <tbody>
                @php $totalRev = $data->sum('revenue'); @endphp
                @forelse($data as $row)
                <tr>
                    <td>{{ $row->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($row->qty ?? 0) }}</td>
                    <td class="text-right">${{ number_format($row->revenue ?? 0, 2) }}</td>
                    <td class="text-right">
                        @php $pct = $totalRev > 0 ? (($row->revenue ?? 0) / $totalRev) * 100 : 0; @endphp
                        {{ number_format($pct, 1) }}%
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No data available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    @else
        {{-- 📋 ទម្រង់ធម្មតា (Revenue / Sales ផ្សេងៗ) --}}
        <h2>Revenue Report</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order ID</th>
                    <th class="text-right">Total Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr>
                    <td>{{ $item->id ?? 'N/A' }}</td>
                    <td>{{ $item->order_id ?? ($item->id ?? 'N/A') }}</td>
                    <td class="text-right">${{ number_format($item->total ?? ($item->amount ?? 0), 2) }}</td>
                    <td>{{ ucfirst($item->status ?? 'Completed') }}</td>
                    <td>{{ isset($item->created_at) ? $item->created_at->format('Y-m-d H:i') : now()->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No data available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    @endif
</body>
</html>