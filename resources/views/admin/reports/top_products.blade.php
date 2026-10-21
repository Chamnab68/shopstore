{{-- resources/views/admin/reports/top_products.blade.php --}}
@extends('layouts.admin')
@section('title','Top Products Report')
@section('page-title','⭐ Top Products Report')

@section('content')
<div class="d-flex justify-content-between mb-4">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>All Reports</a>
    <a href="{{ route('admin.reports.export', 'top-products') }}" class="btn btn-sm btn-danger">
    <i class="fas fa-file-pdf me-1"></i>Export PDF
</a>
    <form method="GET" class="d-flex gap-2">
        <select name="limit" class="form-select form-select-sm" onchange="this.form.submit()">
            @foreach([10,20,50,100] as $l)
            <option value="{{ $l }}" {{ $limit==$l?'selected':'' }}>Top {{ $l }}</option>
            @endforeach
        </select>
    </form>
</div>
@include('admin.components.date-filter')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>#</th><th>Product</th><th>Category</th><th>Price</th><th>Total Sold</th><th>Revenue</th><th>Rating</th></tr></thead>
                <tbody>
                @forelse($data as $i => $p)
                <tr>
                    <td>
                        @if($i < 3)
                        <span style="font-size:20px">{{ ['🥇','🥈','🥉'][$i] }}</span>
                        @else
                        <strong class="text-muted">{{ $i+1 }}</strong>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $p->image_url }}" style="width:40px;height:40px;object-fit:contain;border-radius:6px;background:#f8f9fa">
                            <div>
                                <div class="fw-bold" style="font-size:13px">{{ Str::limit($p->name,40) }}</div>
                                <div class="text-muted" style="font-size:11px">{{ $p->sku }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark">{{ $p->category->name ?? '—' }}</span></td>
                    <td>${{ number_format($p->sale_price ?? $p->price,2) }}</td>
                    <td><span class="badge bg-primary">{{ number_format($p->total_sold) }}</span></td>
                    <td class="text-success fw-bold">${{ number_format($p->revenue,2) }}</td>
                    <td>
                        <div class="text-warning" style="font-size:12px">
                            @for($s=1;$s<=5;$s++)<i class="fa{{ $s<=$p->rating?'s':'r' }} fa-star"></i>@endfor
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No sales data yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
