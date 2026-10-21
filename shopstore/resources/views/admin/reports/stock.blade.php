@extends('layouts.admin')
@section('title','Stock Report')
@section('page-title','📦 Stock Report')

@section('content')
<div class="d-flex justify-content-between mb-4">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>All Reports</a>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reports.stock') }}" class="btn btn-sm {{ !request('filter')?'btn-primary':'btn-outline-primary' }}">All</a>
        <a href="{{ route('admin.reports.stock',['filter'=>'low']) }}" class="btn btn-sm {{ request('filter')==='low'?'btn-warning':'btn-outline-warning' }}">Low Stock</a>
        <a href="{{ route('admin.reports.stock',['filter'=>'out']) }}" class="btn btn-sm {{ request('filter')==='out'?'btn-danger':'btn-outline-danger' }}">Out of Stock</a>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>Product</th><th>Category</th><th>SKU</th><th>Price</th><th>Stock</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                @forelse($data as $p)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $p->image_url }}" style="width:40px;height:40px;object-fit:contain;border-radius:6px;background:#f8f9fa">
                            <div class="fw-bold" style="font-size:13px">{{ Str::limit($p->name,40) }}</div>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark">{{ $p->category->name ?? '—' }}</span></td>
                    <td style="font-size:12px;color:#888">{{ $p->sku }}</td>
                    <td>${{ number_format($p->sale_price ?? $p->price,2) }}</td>
                    <td>
                        @if($p->stock == 0)
                        <span class="fw-bold text-danger fs-5">0</span>
                        @elseif($p->stock < 5)
                        <span class="fw-bold text-danger">{{ $p->stock }}</span>
                        @elseif($p->stock < 10)
                        <span class="fw-bold text-warning">{{ $p->stock }}</span>
                        @else
                        <span class="fw-bold text-success">{{ $p->stock }}</span>
                        @endif
                    </td>
                    <td>
                        @if($p->stock == 0) <span class="badge bg-danger">Out of Stock</span>
                        @elseif($p->stock < 10) <span class="badge bg-warning text-dark">Low Stock</span>
                        @else <span class="badge bg-success">In Stock</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.products.edit',$p->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit me-1"></i>Update Stock
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No products found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $data->links() }}
    </div>
</div>
@endsection
