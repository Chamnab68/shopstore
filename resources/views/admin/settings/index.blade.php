@extends('layouts.admin')
@section('title','Payment Settings')
@section('page-title','💳 Payment & QR Code Settings')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')

{{-- Bank Info --}}
<div class="card mb-4">
<div class="card-body">
    <h6 class="fw-bold mb-4">🏦 Bank Information</h6>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-bold">Bank Name *</label>
            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $settings['bank_name']) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">Account Name *</label>
            <input type="text" name="bank_account_name" class="form-control" value="{{ old('bank_account_name', $settings['bank_account_name']) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">Account Number *</label>
            <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $settings['bank_account_number']) }}" required>
        </div>
    </div>
</div>
</div>

{{-- QR Codes --}}
<div class="row g-4 mb-4">
    {{-- USD QR --}}
    <div class="col-md-6">
        <div class="card h-100">
        <div class="card-body">
            <h6 class="fw-bold mb-3">💵 QR Code — USD ($)</h6>
            @if($settings['bank_qr_usd'])
            <div class="text-center mb-3">
                <div class="d-inline-block position-relative">
                    <img src="{{ asset('storage/'.$settings['bank_qr_usd']) }}"
                         style="width:200px;height:200px;object-fit:contain;border:2px solid #28a745;border-radius:12px;padding:8px;background:white">
                    <button type="button" onclick="deleteQr('usd')"
                            style="position:absolute;top:-10px;right:-10px;background:#dc3545;color:white;border:none;border-radius:50%;width:26px;height:26px;font-size:13px;cursor:pointer">✕</button>
                </div>
                <div class="text-success fw-bold mt-2" style="font-size:13px">✅ USD QR Active</div>
            </div>
            @else
            <div class="text-center mb-3">
                <div style="width:200px;height:200px;border:2px dashed #28a745;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto;background:#f8fff9">
                    <div class="text-center text-muted">
                        <i class="fas fa-dollar-sign fa-3x mb-2" style="color:#28a745"></i>
                        <div style="font-size:12px">No USD QR yet</div>
                    </div>
                </div>
            </div>
            @endif

            <div id="usd-preview" class="text-center mb-3 d-none">
                <img id="usd-preview-img" style="width:200px;height:200px;object-fit:contain;border:2px solid #28a745;border-radius:12px;padding:8px;background:white">
                <div class="text-primary mt-1" style="font-size:12px">New USD QR (not saved)</div>
            </div>

            <div style="border:2px dashed #28a745;border-radius:10px;padding:20px;text-align:center;cursor:pointer"
                 onclick="document.getElementById('usd-input').click()">
                <input type="file" name="bank_qr_usd" id="usd-input" accept="image/*" class="d-none"
                       onchange="previewQr(this,'usd')">
                <i class="fas fa-cloud-upload-alt text-muted mb-1" style="font-size:24px"></i>
                <div style="font-size:13px;font-weight:600">Upload USD QR Code</div>
                <div class="text-muted" style="font-size:11px">JPG, PNG (Max 2MB)</div>
            </div>
        </div>
        </div>
    </div>

    {{-- KHR QR --}}
    <div class="col-md-6">
        <div class="card h-100">
        <div class="card-body">
            <h6 class="fw-bold mb-3">🇰🇭 QR Code — KHR (៛)</h6>
            @if($settings['bank_qr_khr'])
            <div class="text-center mb-3">
                <div class="d-inline-block position-relative">
                    <img src="{{ asset('storage/'.$settings['bank_qr_khr']) }}"
                         style="width:200px;height:200px;object-fit:contain;border:2px solid #dc3545;border-radius:12px;padding:8px;background:white">
                    <button type="button" onclick="deleteQr('khr')"
                            style="position:absolute;top:-10px;right:-10px;background:#dc3545;color:white;border:none;border-radius:50%;width:26px;height:26px;font-size:13px;cursor:pointer">✕</button>
                </div>
                <div class="text-danger fw-bold mt-2" style="font-size:13px">✅ KHR QR Active</div>
            </div>
            @else
            <div class="text-center mb-3">
                <div style="width:200px;height:200px;border:2px dashed #dc3545;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto;background:#fff8f8">
                    <div class="text-center text-muted">
                        <i class="fas fa-kiwi-bird fa-3x mb-2" style="color:#dc3545"></i>
                        <div style="font-size:12px">No KHR QR yet</div>
                    </div>
                </div>
            </div>
            @endif

            <div id="khr-preview" class="text-center mb-3 d-none">
                <img id="khr-preview-img" style="width:200px;height:200px;object-fit:contain;border:2px solid #dc3545;border-radius:12px;padding:8px;background:white">
                <div class="text-primary mt-1" style="font-size:12px">New KHR QR (not saved)</div>
            </div>

            <div style="border:2px dashed #dc3545;border-radius:10px;padding:20px;text-align:center;cursor:pointer"
                 onclick="document.getElementById('khr-input').click()">
                <input type="file" name="bank_qr_khr" id="khr-input" accept="image/*" class="d-none"
                       onchange="previewQr(this,'khr')">
                <i class="fas fa-cloud-upload-alt text-muted mb-1" style="font-size:24px"></i>
                <div style="font-size:13px;font-weight:600">Upload KHR QR Code</div>
                <div class="text-muted" style="font-size:11px">JPG, PNG (Max 2MB)</div>
            </div>
        </div>
        </div>
    </div>
</div>

<div class="d-grid">
    <button type="submit" class="btn btn-primary fw-bold py-3 fs-5">
        <i class="fas fa-save me-2"></i>Save Payment Settings
    </button>
</div>
</form>
</div>
</div>
@endsection

@push('scripts')
<script>
function previewQr(input, type) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById(type+'-preview-img').src = e.target.result;
            document.getElementById(type+'-preview').classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function deleteQr(type) {
    if (!confirm('Remove ' + type.toUpperCase() + ' QR Code?')) return;
    fetch('{{ route("admin.settings.delete-qr") }}?type=' + type, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken }
    }).then(r => r.json()).then(d => {
        if (d.success) { showToast('✅ QR Code removed!'); setTimeout(() => location.reload(), 1000); }
    });
}
</script>
@endpush
