{{-- revenue_by_category.blade.php --}}
@extends('layouts.admin')
@section('title','Revenue by Category')
@section('page-title','🏷️ Revenue by Category')
@section('content')
<div class="d-flex justify-content-between mb-4">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>All Reports</a>
</div>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Revenue Share</h6>
            <canvas id="catPie" height="220"></canvas>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th>Category</th><th>Qty Sold</th><th>Revenue</th><th>Share</th></tr></thead>
                        <tbody>
                        @php $totalRev = $data->sum('revenue'); @endphp
                        @forelse($data as $row)
                        <tr>
                            <td><strong>{{ $row->name }}</strong></td>
                            <td>{{ number_format($row->qty) }}</td>
                            <td class="text-success fw-bold">${{ number_format($row->revenue,2) }}</td>
                            <td>
                                @php $pct = $totalRev > 0 ? ($row->revenue/$totalRev)*100 : 0; @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px">
                                        <div class="progress-bar bg-primary" style="width:{{ $pct }}%"></div>
                                    </div>
                                    <span style="font-size:12px;white-space:nowrap">{{ number_format($pct,1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No data.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
const cd = @json($data);
new Chart(document.getElementById('catPie').getContext('2d'),{
    type:'doughnut',
    data:{labels:cd.map(d=>d.name),datasets:[{data:cd.map(d=>d.revenue),backgroundColor:['#6c3fd6','#ff6b35','#28a745','#17a2b8','#ffc107','#dc3545','#6c757d','#343a40','#e83e8c']}]},
    options:{responsive:true,plugins:{legend:{position:'bottom'}}}
});
</script>
@endpush
