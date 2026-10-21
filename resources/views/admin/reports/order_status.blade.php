{{-- order_status.blade.php --}}
@extends('layouts.admin')
@section('title','Order Status Report')
@section('page-title','📋 Order Status Report')
@section('content')
<div class="mb-4"><a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary">
    <i class="fas fa-arrow-left me-1"></i>All Reports</a>
<a href="{{ route('admin.reports.export', 'order-status') }}" class="btn btn-sm btn-danger">
    <i class="fas fa-file-pdf me-1"></i>Export PDF
</a>
</div>
@include('admin.components.date-filter')
<div class="row g-4">
    <div class="col-lg-5"><div class="card p-4"><h6 class="fw-bold mb-3">Orders by Status</h6><canvas id="statusChart" height="250"></canvas></div></div>
    <div class="col-lg-7">
        <div class="card"><div class="card-body">
            <table class="table align-middle">
                <thead><tr><th>Status</th><th>Orders</th><th>Total Value</th><th>Share</th></tr></thead>
                <tbody>
                @php $total = $data->sum('count'); @endphp
                @foreach($data as $row)
                <tr>
                    <td><span class="badge bg-{{ ['pending'=>'warning','confirmed'=>'info','processing'=>'primary','shipped'=>'secondary','delivered'=>'success','cancelled'=>'danger'][$row->status] ?? 'light' }}">{{ ucfirst($row->status) }}</span></td>
                    <td><strong>{{ $row->count }}</strong></td>
                    <td>${{ number_format($row->total,2) }}</td>
                    <td>{{ $total > 0 ? number_format(($row->count/$total)*100,1) : 0 }}%</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div></div>
    </div>
</div>
@endsection
@push('scripts')
<script>
const sd = @json($data);
const colors = {pending:'#ffc107',confirmed:'#17a2b8',processing:'#6c3fd6',shipped:'#6c757d',delivered:'#28a745',cancelled:'#dc3545'};
new Chart(document.getElementById('statusChart').getContext('2d'),{
    type:'pie',
    data:{labels:sd.map(d=>d.status.charAt(0).toUpperCase()+d.status.slice(1)),datasets:[{data:sd.map(d=>d.count),backgroundColor:sd.map(d=>colors[d.status]||'#ccc')}]},
    options:{responsive:true,plugins:{legend:{position:'bottom'}}}
});
</script>
@endpush
