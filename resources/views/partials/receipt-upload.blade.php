{{-- resources/views/partials/receipt-upload.blade.php --}}
<div class="upload-area border rounded-3 p-4 text-center mb-3"
     style="border-style:dashed!important;cursor:pointer;border-color:#ccc;transition:border-color .2s"
     onclick="document.getElementById('receipt-file-{{ $orderId }}').click()"
     ondragover="event.preventDefault();this.style.borderColor='#6c3fd6'"
     ondragleave="this.style.borderColor='#ccc'"
     ondrop="handleReceiptDrop(event,{{ $orderId }})">
    <input type="file" id="receipt-file-{{ $orderId }}"
           accept="image/*" class="d-none"
           onchange="handleFileSelect(this)">
    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
    <div class="fw-bold">Click or drag & drop here</div>
    <div class="text-muted" style="font-size:12px">Supports: JPG, PNG (Max 5MB)</div>
</div>

{{-- Preview --}}
<div id="preview-area" class="d-none text-center mb-3">
    <img id="preview-img"
         style="max-width:100%;max-height:200px;border-radius:8px;border:1px solid #eee;object-fit:contain">
    <div class="text-muted mt-1" style="font-size:12px">Preview — not uploaded yet</div>
</div>

{{-- Upload Button --}}
<button class="btn btn-success w-100 fw-bold py-2 upload-btn"
        onclick="uploadReceipt(this)" disabled>
    <i class="fas fa-upload me-2"></i>Upload Receipt & Process Order
</button>

<script>
function handleReceiptDrop(e, orderId) {
    e.preventDefault();
    e.currentTarget.style.borderColor = '#ccc';
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('receipt-file-' + orderId).files = dt.files;
        handleFileSelect(document.getElementById('receipt-file-' + orderId));
    }
}
</script>
