@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-4">
                    <h3 class="mb-2 font-weight-bold">Bakong KHQR Payment</h3>
                    <p class="text-muted">Order ID: <strong>{{ $order->order_number }}</strong></p>
                    
                    <!-- បង្ហាញទឹកប្រាក់ជាក់ស្តែងដែលត្រូវបង់ -->
                    <h4 class="text-primary mb-3">Total Amount: ${{ number_format($order->total, 2) }}</h4>

                    <!-- បង្ហាញ Dynamic QR Code ដែល generate រួច -->
                    <!-- កូដត្រឹមត្រូវសម្រាប់បង្ហាញ Base64 QR Code Image -->
<!-- កូដត្រឹមត្រូវសម្រាប់បង្ហាញ SVG QR Code -->
<div class="my-3">
    <div class="p-3 border d-inline-block bg-white rounded shadow-sm">
        {!! $qrSvg !!}
    </div>
</div>

                    <div class="alert alert-info py-2 small mb-4">
                        <i class="fas fa-info-circle"></i> QR Code នេះត្រូវបានបង្កើតឡើងស្វ័យប្រវត្តិតាមទឹកប្រាក់ **${{ number_format($order->total, 2) }}** របស់អ្នក។ សូមប្រើប្រាស់ App ធនាគារណាមួយដើម្បីស្កេន។
                    </div>

                    <form action="{{ route('orders.process', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm">
                            I have paid (Simulate Success)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection