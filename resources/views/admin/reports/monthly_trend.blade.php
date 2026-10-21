@extends('layouts.admin')
@section('title','Monthly Revenue Trend')
@section('page-title','📈 Monthly Revenue Trend')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>All Reports</a>
</div>
@include('admin.components.date-filter')
<div class="card mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Revenue & Orders (Last 12 Months)</h6>
        <canvas id="trendChart" height="80"></canvas>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Month</th><th>Orders</th><th>Revenue</th><th>Growth</th></tr></thead>
                <tbody>
                @php $months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; $prev=0; @endphp
                @foreach($data as $i => $row)
                @php $growth = $prev > 0 ? (($row->revenue - $prev) / $prev) * 100 : 0; $prev = $row->revenue; @endphp
                <tr>
                    <td><strong>{{ $months[$row->month-1] }} {{ $row->year }}</strong></td>
                    <td>{{ $row->orders }}</td>
                    <td><strong class="text-success">${{ number_format($row->revenue,2) }}</strong></td>
                    <td>
                        @if($i > 0)
                        <span class="badge {{ $growth >= 0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth,1) }}%
                        </span>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const d = @json($data);
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
new Chart(document.getElementById('trendChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: d.map(r => months[r.month-1]+' '+r.year),
        datasets: [{
            label: 'Revenue ($)', data: d.map(r => r.revenue),
            borderColor:'#6c3fd6', backgroundColor:'rgba(108,63,214,.1)', fill:true, tension:0.4, yAxisID:'y',
        },{
            label: 'Orders', data: d.map(r => r.orders),
            borderColor:'#ff6b35', backgroundColor:'transparent', tension:0.4, yAxisID:'y2',
        }]
    },
    options: {
        responsive:true, interaction:{mode:'index'},
        scales:{
            y:{ beginAtZero:true, ticks:{callback:v=>'$'+v.toLocaleString()} },
            y2:{ position:'right', beginAtZero:true, grid:{drawOnChartArea:false} }
        }
    }
});
</script>
@endpush
