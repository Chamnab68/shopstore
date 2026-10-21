@extends('layouts.admin')
@section('title','Payment Report')
@section('page-title','💳 Payment Report')
@section('content')
<div class="d-flex justify-content-between mb-4">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>All Reports</a>
    <a href="{{ route('admin.reports.export', 'payment') }}" class="btn btn-sm btn-danger">
    <i class="fas fa-file-pdf me-1"></i>Export PDF
</a>
</div>
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label fw-bold" style="font-size:12px">From</label><input type="date" name="date_from" class="form-control" value="{{ $from }}"></div>
            <div class="col-md-3"><label class="form-label fw-bold" style="font-size:12px">To</label><input type="date" name="date_to" class="form-control" value="{{ $to }}"></div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Payment Method</th><th>Payment Status</th><th>Count</th><th>Total Amount</th></tr></thead>
                <tbody>
                @forelse($data as $row)
                <tr>
                    <td><strong>{{ ucwords(str_replace('_',' ',$row->payment_method)) }}</strong></td>
                    <td><span class="badge bg-{{ $row->payment_status==='paid'?'success':($row->payment_status==='refunded'?'warning':'danger') }}">{{ ucfirst($row->payment_status) }}</span></td>
                    <td>{{ $row->count }}</td>
                    <td class="fw-bold">${{ number_format($row->total,2) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No payment data.</td></tr>
                @endforelse
                </tbody>
                <tfoot class="fw-bold">
                    <tr><td colspan="2">TOTAL</td><td>{{ $data->sum('count') }}</td><td>${{ number_format($data->sum('total'),2) }}</td></tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
