@extends('layouts.admin')
@section('title','Sales Summary Report')
@section('page-title','📊 Sales Summary Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>All Reports
    </a>
    <a href="{{ route('admin.reports.export','sales') }}" class="btn btn-sm btn-danger">
        <i class="fas fa-file-pdf me-1"></i>Export PDF
    </a>
</div>

{{-- Date Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold" style="font-size:12px">From Date</label>
                <input type="date" name="date_from" class="form-control" value="{{ $from }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold" style="font-size:12px">To Date</label>
                <input type="date" name="date_to" class="form-control" value="{{ $to }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Apply Filter</button>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted" style="font-size:12px">Total Orders</div>
            <div class="fw-bold fs-4 text-primary">{{ $data->sum('orders') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted" style="font-size:12px">Total Revenue</div>
            <div class="fw-bold fs-4 text-success">${{ number_format($data->sum('revenue'),2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted" style="font-size:12px">Total Discounts</div>
            <div class="fw-bold fs-4 text-danger">-${{ number_format($data->sum('discounts'),2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted" style="font-size:12px">Avg Order Value</div>
            <div class="fw-bold fs-4 text-info">${{ $data->sum('orders') > 0 ? number_format($data->sum('revenue') / $data->sum('orders'),2) : '0.00' }}</div>
        </div>
    </div>
</div>

{{-- Chart --}}
<div class="card mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Daily Revenue Chart</h6>
        <canvas id="salesChart" height="80"></canvas>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Daily Breakdown</h6>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Orders</th>
                        <th>Revenue</th>
                        <th>Discounts</th>
                        <th>Avg Order</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($data as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->date)->format('d M Y (D)') }}</td>
                    <td><span class="badge bg-primary">{{ $row->orders }}</span></td>
                    <td><strong class="text-success">${{ number_format($row->revenue,2) }}</strong></td>
                    <td class="text-danger">-${{ number_format($row->discounts,2) }}</td>
                    <td>${{ $row->orders > 0 ? number_format($row->revenue/$row->orders,2) : '0.00' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No data for this period.</td></tr>
                @endforelse
                </tbody>
                @if($data->count())
                <tfoot class="fw-bold">
                    <tr>
                        <td>TOTAL</td>
                        <td>{{ $data->sum('orders') }}</td>
                        <td class="text-success">${{ number_format($data->sum('revenue'),2) }}</td>
                        <td class="text-danger">-${{ number_format($data->sum('discounts'),2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const chartData = @json($data);
new Chart(document.getElementById('salesChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: chartData.map(d => d.date),
        datasets: [{
            label: 'Revenue ($)',
            data: chartData.map(d => d.revenue),
            backgroundColor: 'rgba(108,63,214,.7)',
            borderRadius: 6,
        },{
            label: 'Orders',
            data: chartData.map(d => d.orders),
            type: 'line',
            borderColor: '#ff6b35',
            backgroundColor: 'transparent',
            yAxisID: 'y2',
            tension: 0.4,
        }]
    },
    options: {
        responsive: true,
        interaction: { mode:'index' },
        scales: {
            y: { beginAtZero:true, ticks:{ callback: v=>'$'+v } },
            y2: { position:'right', beginAtZero:true, grid:{drawOnChartArea:false} }
        }
    }
});
</script>
@endpush
