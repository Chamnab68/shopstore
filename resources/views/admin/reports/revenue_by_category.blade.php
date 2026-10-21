{{-- revenue_by_category.blade.php --}}
@extends('layouts.admin')
@section('title','Revenue by Category')
@section('page-title','🏷️ Revenue by Category')

@section('content')
<div class="d-flex justify-content-between mb-4">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>All Reports
    </a>
    <a href="{{ route('admin.reports.export','revenue-category') }}" class="btn btn-sm btn-danger">
    <i class="fas fa-file-pdf me-1"></i>Export PDF
</a>
</div>


@include('admin.components.date-filter')

<div class="row g-4">
    <!-- ផ្នែកបង្ហាញក្រាប (Chart) ឱ្យស៊ីចង្វាក់គ្នា -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h6 class="fw-bold mb-3 text-dark">Revenue Share</h6>
            <div class="d-flex justify-content-center align-items-center" style="position: relative; height: 280px;">
                <canvas id="catPie"></canvas>
            </div>
        </div>
    </div>

    <!-- ផ្នែកតារាងបង្ហាញទិន្នន័យ -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Qty Sold</th>
                                <th>Revenue</th>
                                <th>Share</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php $totalRev = $data->sum('revenue'); @endphp
                        @forelse($data as $row)
                        <tr>
                            <td><strong>{{ $row->name }}</strong></td>
                            <td>{{ number_format($row->qty) }}</td>
                            <td class="text-success fw-bold">${{ number_format($row->revenue, 2) }}</td>
                            <td>
                                @php $pct = $totalRev > 0 ? ($row->revenue / $totalRev) * 100 : 0; @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span style="font-size: 12px; white-space: nowrap;">{{ number_format($pct, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No data available for the selected period.</td>
                        </tr>
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
document.addEventListener("DOMContentLoaded", function() {
    const cd = @json($data);
    
    if (cd && cd.length > 0) {
        const ctx = document.getElementById('catPie').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: cd.map(d => d.name),
                datasets: [{
                    data: cd.map(d => d.revenue),
                    backgroundColor: [
                        '#6c3fd6', '#ff6b35', '#28a745', '#17a2b8', 
                        '#ffc107', '#dc3545', '#6c757d', '#343a40', '#e83e8c'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush