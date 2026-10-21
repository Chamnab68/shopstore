@extends('layouts.app')
@section('title', 'Payment - ' . $order->order_number)

@section('content')
<style>
.qr-wrapper {
    background: white;
    border-radius: 20px;
    padding: 16px;
    box-shadow: 0 8px 30px rgba(108,63,214,.2);
    border: 3px solid #6c3fd6;
    display: inline-block;
    transition: all .3s;
}
.qr-wrapper:hover { transform: scale(1.02); box-shadow: 0 12px 40px rgba(108,63,214,.35); }
.qr-wrapper img { width: 260px; height: 260px; object-fit: contain; display: block; border-radius: 10px; }
.btn-currency { border-radius: 12px; padding: 10px 24px; font-weight: 700; font-size: 15px; transition: all .2s; }
.btn-currency.active-usd { background: #28a745; color: white; border: 2px solid #28a745; box-shadow: 0 4px 15px rgba(40,167,69,.3); }
.btn-currency.inactive-usd { background: white; color: #28a745; border: 2px solid #28a745; }
.btn-currency.active-khr { background: #dc3545; color: white; border: 2px solid #dc3545; box-shadow: 0 4px 15px rgba(220,53,69,.3); }
.btn-currency.inactive-khr { background: white; color: #dc3545; border: 2px solid #dc3545; }
.upload-area { border: 2px dashed #6c3fd6; border-radius: 16px; padding: 40px 20px; text-align: center; cursor: pointer; transition: all .2s; background: #fafafa; }
.upload-area:hover { border-color: #5930b8; background: #f0ebff; }
.bank-info-card { background: linear-gradient(135deg, #6c3fd6, #a855f7); border-radius: 16px; padding: 20px; color: white; margin-bottom: 20px; }
.bank-info-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.bank-info-label { opacity: .8; font-size: 13px; }
.bank-info-value { font-weight: 700; font-size: 15px; }
.account-number { font-size: 24px; font-weight: 900; letter-spacing: 2px; text-align: center; padding: 10px; background: rgba(255,255,255,.2); border-radius: 10px; margin-top: 8px; }
</style>

<div class="container py-4">
<div class="row justify-content-center">
<div class="col-lg-10">

    {{-- Header --}}
    <div class="text-center mb-4">
        <div style="width:64px;height:64px;background:#fff3cd;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
            <i class="fas fa-clock fa-2x text-warning"></i>
        </div>
        <h3 class="fw-bold mb-1">Complete Your Payment</h3>
        <p class="text-muted">
            Order <strong class="text-primary">{{ $order->order_number }}</strong> —
            Total: <strong class="text-primary fs-4">${{ number_format($order->total, 2) }}</strong>
        </p>
    </div>

    {{-- CASH ON DELIVERY --}}
    @if(isset($order->payment_method) && $order->payment_method === 'cash_on_delivery')
    <div class="bg-white rounded-4 shadow p-5 text-center" style="max-width:500px;margin:0 auto">
        <div style="width:90px;height:90px;background:#d4edda;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
            <i class="fas fa-money-bill-wave fa-3x text-success"></i>
        </div>
        <h4 class="fw-bold text-success mb-2">Cash on Delivery</h4>
        <p class="text-muted mb-4">
            You have selected cash payment on delivery.<br>
            Please prepare <strong class="text-primary fs-5">${{ number_format($order->total, 2) }}</strong><br>
            to pay the courier upon receiving your package.
        </p>
        <div class="alert alert-info text-start mb-4">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Order:</strong> {{ $order->order_number }}<br>
            <strong>Phone:</strong> {{ $order->shipping_phone ?? 'N/A' }}<br>
            <strong>Address:</strong> {{ $order->shipping_address ?? 'N/A' }}
        </div>
        <form action="{{ route('orders.process', $order->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success fw-bold py-3 px-5 fs-5 w-100 rounded-3">
                <i class="fas fa-check-circle me-2"></i>Process Order
            </button>
        </form>
    </div>

    @else
    {{-- BANK TRANSFER / BAKONG QR --}}
    <div class="row g-4">

        {{-- LEFT: QR Code & Bank Info --}}
        <div class="col-lg-6">
            <div class="bg-white rounded-4 shadow p-4 text-center h-100">

                @if(isset($bankInfo))
                {{-- Bank Info Card (ពី Admin Settings) --}}
                <div class="bank-info-card">
                    <div class="bank-info-row">
                        <span class="bank-info-label"><i class="fas fa-university me-1"></i> Bank</span>
                        <span class="bank-info-value">{{ $bankInfo['bank_name'] ?? 'ABA Bank' }}</span>
                    </div>
                    <div class="bank-info-row">
                        <span class="bank-info-label"><i class="fas fa-user me-1"></i> Name</span>
                        <span class="bank-info-value">{{ $bankInfo['bank_account_name'] ?? 'MY SHOP' }}</span>
                    </div>
                    <div class="account-number">{{ $bankInfo['bank_account_number'] ?? '000 000 000' }}</div>
                </div>

                {{-- Currency Toggle --}}
                <div class="d-flex gap-3 justify-content-center mb-4">
                    <button type="button" onclick="showQR('usd')" id="btn-usd" class="btn-currency active-usd">
                        <i class="fas fa-dollar-sign me-1"></i> USD ($)
                    </button>
                    <button type="button" onclick="showQR('khr')" id="btn-khr" class="btn-currency inactive-khr">
                        <i class="fas fa-coins me-1"></i> KHR (៛)
                    </button>
                </div>

                {{-- USD QR --}}
                <div id="qr-usd">
                    @if(!empty($bankInfo['bank_qr_usd']))
                    <div class="qr-wrapper" style="border-color:#28a745;box-shadow:0 8px 30px rgba(40,167,69,.25)">
                        <img src="{{ asset('storage/'.$bankInfo['bank_qr_usd']) }}" alt="USD QR Code">
                    </div>
                    @else
                    <div class="qr-wrapper" style="border-color:#28a745;">
                        <img src="{{ $qrImageUrl ?? '' }}" alt="Bakong KHQR">
                    </div>
                    @endif
                    <div class="mt-3">
                        <span style="background:#d4edda;color:#155724;padding:6px 18px;border-radius:20px;font-weight:700;font-size:14px">
                            <i class="fas fa-qrcode me-1"></i> Scan to Pay Total: ${{ number_format($order->total, 2) }}
                        </span>
                    </div>
                </div>

                {{-- KHR QR --}}
                <div id="qr-khr" style="display:none">
                    @if(!empty($bankInfo['bank_qr_khr']))
                    <div class="qr-wrapper" style="border-color:#dc3545;box-shadow:0 8px 30px rgba(220,53,69,.25)">
                        <img src="{{ asset('storage/'.$bankInfo['bank_qr_khr']) }}" alt="KHR QR Code">
                    </div>
                    <div class="mt-3">
                        <span style="background:#f8d7da;color:#721c24;padding:6px 18px;border-radius:20px;font-weight:700;font-size:14px">
                            <i class="fas fa-qrcode me-1"></i> Scan to Pay in KHR (៛)
                        </span>
                    </div>
                    @else
                    <div class="text-muted p-4">KHR QR code not configured</div>
                    @endif
                </div>

                @else
                {{-- Trường hợpប្រើប្រាស់ Bakong API សុទ្ធសាធ (តាមរូបដំបូង) --}}
                <h4 class="fw-bold text-primary mb-3">Scan to Pay via Bakong</h4>
                <p class="text-muted">Scan using ABA Mobile or Bakong App</p>
                <div class="my-3">
                    <img src="{{ $qrImageUrl }}" alt="KHQR Payment" class="img-fluid border rounded p-2 shadow-sm" style="width: 250px; height: 250px;">
                </div>
                @endif

                {{-- 🔴 ប៊ូតុង Demo សម្រាប់ចុចតេស្តជាមួយគ្រូ --}}
                <div class="mt-4 pt-3 border-top">
                    <form action="{{ route('orders.simulate-paid', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow">
                            <i class="fas fa-check-circle me-1"></i> [DEMO] Simulate Payment Success
                        </button>
                    </form>
                </div>

                {{-- Reference --}}
                <div class="alert alert-warning mt-3 text-start mb-0" style="font-size:13px;border-radius:12px">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Reference:</strong> {{ $order->order_number }}
                </div>
            </div>
        </div>

        {{-- RIGHT: Upload Receipt & Order Summary --}}
        <div class="col-lg-6">
            <div class="bg-white rounded-4 shadow p-4 h-100">
                <h5 class="fw-bold mb-2"><i class="fas fa-camera me-1"></i> Upload Payment Receipt</h5>
                <p class="text-muted mb-4" style="font-size:13px">
                    After scanning and transferring, please screenshot your receipt and upload it here.
                </p>

                @if(!empty($order->receipt_image))
                {{-- Already uploaded --}}
                <div class="text-center mb-3">
                    <img src="{{ asset('storage/'.$order->receipt_image) }}" style="max-width:100%;border-radius:12px;border:2px solid #28a745;max-height:220px;object-fit:contain;">
                    <div class="text-success fw-bold mt-2">
                        <i class="fas fa-check-circle me-1"></i>Receipt uploaded successfully!
                    </div>
                </div>
                <div class="alert alert-success rounded-3" style="font-size:13px">
                    <i class="fas fa-hourglass-half me-1"></i> Waiting for admin to verify payment.
                </div>
                @else
                {{-- Upload area --}}
                <div class="upload-area" id="upload-area" onclick="document.getElementById('receipt-file').click()">
                    <input type="file" id="receipt-file" accept="image/*" class="d-none" onchange="handleFileSelect(this)">
                    <i class="fas fa-cloud-upload-alt fa-3x mb-3" style="color:#6c3fd6;opacity:.6"></i>
                    <div class="fw-bold fs-5 mb-1">Click to upload receipt image</div>
                    <div class="text-muted">Supports: JPG, PNG</div>
                </div>

                <div id="preview-area" class="d-none text-center mt-3">
                    <img id="preview-img" style="max-width:100%;max-height:180px;border-radius:12px;border:2px solid #6c3fd6;object-fit:contain;">
                </div>

                <button class="btn btn-success w-100 fw-bold py-3 mt-3 rounded-3" id="upload-btn" onclick="uploadReceipt(this)" disabled>
                    <i class="fas fa-upload me-2"></i>Confirm & Upload Receipt
                </button>
                @endif

                <hr class="my-4">

                {{-- Order Summary --}}
                <h6 class="fw-bold mb-3"><i class="fas fa-list-alt me-1"></i> Order Summary</h6>
                <div style="background:#f8f9fa;border-radius:12px;padding:16px">
                    @if(isset($order->items))
                        @foreach($order->items as $item)
                        <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                            <span>{{ Str::limit($item->product_name ?? 'Product', 30) }} × {{ $item->quantity }}</span>
                            <strong>${{ number_format($item->subtotal ?? 0, 2) }}</strong>
                        </div>
                        @endforeach
                    @endif
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <strong class="fs-5">Total</strong>
                        <strong class="fs-5 text-primary">${{ number_format($order->total, 2) }}</strong>
                    </div>
                </div>

                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm w-100 mt-3 rounded-3">
                    <i class="fas fa-eye me-1"></i>View Order Details
                </a>
            </div>
        </div>
    </div>
    @endif

</div>
</div>
</div>
@endsection

@push('scripts')
<script>
const orderId = {{ $order->id }};
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let selectedFile = null;

function showQR(type) {
    document.getElementById('qr-usd').style.display = type === 'usd' ? 'block' : 'none';
    document.getElementById('qr-khr').style.display = type === 'khr' ? 'block' : 'none';
    document.getElementById('btn-usd').className = 'btn-currency ' + (type === 'usd' ? 'active-usd' : 'inactive-usd');
    document.getElementById('btn-khr').className = 'btn-currency ' + (type === 'khr' ? 'active-khr' : 'inactive-khr');
}

function handleFileSelect(input) {
    selectedFile = input.files[0];
    if (selectedFile) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-area').classList.remove('d-none');
        };
        reader.readAsDataURL(selectedFile);
        document.getElementById('upload-btn').disabled = false;
    }
}

function uploadReceipt(btn) {
    if (!selectedFile) return;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';

    const formData = new FormData();
    formData.append('receipt', selectedFile);
    formData.append('_token', csrf);

    fetch(`/orders/${orderId}/receipt`, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                alert('Receipt uploaded successfully!');
                location.reload();
            } else {
                alert('Upload failed. Try again.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-upload me-2"></i>Confirm & Upload Receipt';
            }
        }).catch(err => {
            alert('Something went wrong!');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-upload me-2"></i>Confirm & Upload Receipt';
        });
}
</script>
@endpush