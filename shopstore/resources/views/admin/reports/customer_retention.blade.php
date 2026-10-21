{{-- customer_retention.blade.php --}}
@extends('layouts.admin')
@section('title','Customer Retention')
@section('page-title','🔄 Customer Retention Report')
@section('content')
<div class="mb-4"><a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>All Reports</a></div>
<div class="card mb-4"><div class="card-body"><h6 class="fw-bold mb-3">New vs Returning Customers (Last 12 Months)</h6><canvas id="retentionChart" height="80"></canvas></div></div>
<div class="card"><div class="card-body">
    <table class="table">
        <thead><tr><th>Month</th><th>New Customers</th><th>Returning Customers</th><th>Retention Rate</th></tr></thead>
        <tbody>
        @foreach($data as $row)
        <tr>
            <td>{{ $row->month }}</td>
            <td><span class="badge bg-success">{{ $row->new_customers }}</span></td>
            <td><span class="badge bg-primary">{{ $row->returning_customers }}</span></td>
            <td>
                @php $total = $row->new_customers + $row->returning_customers; @endphp
                {{ $total > 0 ? number_format(($row->returning_customers/$total)*100,1) : 0 }}%
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div></div>
@endsection
@push('scripts')
<script>
const rd = @json($data);
new Chart(document.getElementById('retentionChart').getContext('2d'),{
    type:'bar',
    data:{
        labels:rd.map(d=>d.month),
        datasets:[
            {label:'New',data:rd.map(d=>d.new_customers),backgroundColor:'#28a745',borderRadius:4},
            {label:'Returning',data:rd.map(d=>d.returning_customers),backgroundColor:'#6c3fd6',borderRadius:4}
        ]
    },
    options:{responsive:true,scales:{x:{stacked:false},y:{beginAtZero:true}}}
});
</script>
@endpush
