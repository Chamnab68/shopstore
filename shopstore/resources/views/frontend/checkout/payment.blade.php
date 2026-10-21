{{-- resources/views/frontend/checkout/payment.blade.php --}}
@extends('layouts.app')
@section('title', 'Payment - Order #{{ $order->order_number }}')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-4">
                <div style="width:70px;height:70px;background:#fff3cd;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                    <i class="fas fa-clock fa-2x text-warning"></i>
                </div>
                <h3 class="fw-bold">Complete Your Payment</h3>
                <p class="text-muted">Order <strong>{{ $order->order_number }}</strong> — Total: <strong class="text-primary fs-5">${{ number_format($order->total, 2) }}</strong></p>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="bg-white rounded-3 shadow-sm p-4 text-center">
                        <h5 class="fw-bold mb-3">🏦 Bank Transfer Details</h5>
                        <div class="border rounded-3 p-3 mb-3 bg-light">
                            <div class="mb-2"><span class="text-muted">Bank:</span> <strong>{{ $bankInfo['name'] }}</strong></div>
                            <div class="mb-2"><span class="text-muted">Account Name:</span> <strong>{{ $bankInfo['account_name'] }}</strong></div>
                            <div class="mb-2"><span class="text-muted">Account Number:</span></div>
                            <div class="fs-4 fw-bold text-primary">{{ $bankInfo['account_number'] }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted mb-2" style="font-size:13px">Scan QR Code to Pay</div>
                            {{-- QR Code Placeholder --}}
                            <div style="width:200px;height:200px;background:#f0f0f0;border-radius:12px;margin:0 auto;display:flex;align-items:center;justify-content:center;border:2px dashed #ccc">
                                <div class="text-center text-muted">
                                    <i class="fas fa-qrcode fa-3x mb-2"></i>
                                    <div style="font-size:12px">Bank QR Code</div>
                                    <div style="font-size:11px">Configure in .env</div>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-warning" style="font-size:13px">
                            <strong>Reference:</strong> {{ $order->order_number }}<br>
                            Please include the order number in your transfer description.
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="bg-white rounded-3 shadow-sm p-4">
                        <h5 class="fw-bold mb-3">📸 Upload Payment Receipt</h5>
                        <p class="text-muted" style="font-size:13px">After making the payment, please upload your receipt screenshot to confirm your payment.</p>

                        @if($order->receipt_image)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>Receipt uploaded successfully!
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$order->receipt_image) }}" style="max-width:100%;border-radius:8px">
                            </div>
                        </div>
                        @else
                        <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center mb-3" id="upload-area"
                             style="border-color:#ccc;cursor:pointer;border-style:dashed">
                            <input type="file" id="receipt-file" accept="image/*" class="d-none">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                            <div class="fw-bold">Click or drag & drop here</div>
                            <div class="text-muted" style="font-size:12px">Supports: JPG, PNG (Max 5MB)</div>
                        </div>

                        <div id="preview-area" class="d-none mb-3">
                            <img id="preview-img" style="max-width:100%;border-radius:8px;border:1px solid #eee">
                        </div>

                        <button class="btn btn-success w-100 fw-bold py-2" id="upload-btn" onclick="uploadReceipt()" disabled>
                            <i class="fas fa-upload me-2"></i>Upload Receipt
                        </button>
                        @endif

                        <hr>
                        <div class="text-center">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>View Order Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="bg-white rounded-3 shadow-sm p-4 mt-4">
                <h5 class="fw-bold mb-3">Order Summary</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Product</th><th>Qty</th><th class="text-end">Price</th></tr></thead>
                        <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr><td colspan="2" class="text-end">Shipping</td><td class="text-end">${{ number_format($order->shipping_fee, 2) }}</td></tr>
                        <tr><td colspan="2" class="text-end fw-bold">Total</td><td class="text-end fw-bold text-primary">${{ number_format($order->total, 2) }}</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const orderId = {{ $order->id }};
const csrf = document.querySelector('meta[name="csrf-token"]').content;
let selectedFile = null;

document.getElementById('upload-area')?.addEventListener('click', () => document.getElementById('receipt-file').click());

document.getElementById('receipt-file')?.addEventListener('change', function() {
    selectedFile = this.files[0];
    if (selectedFile) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-area').classList.remove('d-none');
        };
        reader.readAsDataURL(selectedFile);
        document.getElementById('upload-btn').disabled = false;
    }
});

// Drag & Drop
const uploadArea = document.getElementById('upload-area');
uploadArea?.addEventListener('dragover', e => { e.preventDefault(); uploadArea.style.borderColor='var(--primary)'; });
uploadArea?.addEventListener('dragleave', () => uploadArea.style.borderColor='#ccc');
uploadArea?.addEventListener('drop', e => {
    e.preventDefault();
    uploadArea.style.borderColor='#ccc';
    const file = e.dataTransfer.files[0];
    if (file) {
        document.getElementById('receipt-file').files = e.dataTransfer.files;
        document.getElementById('receipt-file').dispatchEvent(new Event('change'));
    }
});

function uploadReceipt() {
    if (!selectedFile) return;
    const btn = document.getElementById('upload-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';

    const formData = new FormData();
    formData.append('receipt', selectedFile);
    formData.append('_token', csrf);

    fetch('/orders/' + orderId + '/receipt', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                showToast('✅ Receipt uploaded! We will verify your payment.', 'success');
                setTimeout(() => location.reload(), 2000);
            } else {
                showToast('❌ Upload failed. Try again.', 'danger');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-upload me-2"></i>Upload Receipt';
            }
        });
}
</script>
@endpush
