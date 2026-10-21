{{-- resources/views/frontend/checkout/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Checkout - ShopStore')

@push('styles')
<!-- ភ្ជាប់ CSS របស់ Leaflet Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Checkout</h2>
    <form action="{{ route('orders.place') }}" method="POST" id="checkout-form">
        @csrf
        <div class="row g-4">
            <div class="col-lg-7">
                {{-- Shipping Information --}}
                <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold mb-3">Shipping Information</h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name *</label>
                        <input type="text" name="shipping_name" class="form-control" value="{{ auth()->user()->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone Number *</label>
                        <input type="text" name="shipping_phone" class="form-control" value="{{ auth()->user()->phone ?? '' }}" required>
                    </div>
                    
                    {{-- Delivery Address & Google/Leaflet Map Picker --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Delivery Address *</label>
                        <textarea name="shipping_address" id="shipping_address" class="form-control mb-2" rows="2" placeholder="House No, Street, Sangkat, Khan..." required>{{ auth()->user()->address ?? '' }}</textarea>
                        
                        <!-- 🗺️ Map Container -->
                        <div id="map" style="height: 250px; border-radius: 8px;" class="border mb-2"></div>
                        <small class="text-muted">👉 សូមចុចលើផែនទីដើម្បីជ្រើសរើសទីតាំងដឹកជញ្ជូនរបស់អ្នក។</small>

                        <!-- Hidden Inputs សម្រាប់រក្សាទុក Latitude និង Longitude -->
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Order Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Special instructions..."></textarea>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="bg-white rounded-3 shadow-sm p-4">
                    <h5 class="fw-bold mb-3">Payment Method</h5>

                    <!-- Bakong Payment Method Option -->
                    <div class="form-check custom-option mb-3 border p-3 rounded">
                        <input class="form-check-input" type="radio" name="payment_method" id="bakong" value="bakong" onchange="togglePaymentDetails()">
                        <label class="form-check-label d-flex align-items-center cursor-pointer" for="bakong">
                            <div class="me-3">
                                <i class="fas fa-qrcode fa-2x text-primary"></i>
                            </div>
                            <div>
                                <span class="fw-bold d-block">Bakong KHQR Instant Payment</span>
                                <small class="text-muted">Scan with any mobile banking app for instant automated verification.</small>
                            </div>
                        </label>
                    </div>
                    
                    {{-- Option 2: Cash on Delivery --}}
                    <div class="form-check mb-3 p-3 border rounded-3 payment-option-card">
                        <input class="form-check-input" type="radio" name="payment_method" value="cash_on_delivery" id="cod" onchange="togglePaymentDetails()">
                        <label class="form-check-label fw-bold" for="cod">
                            <i class="fas fa-money-bill-wave me-2 text-success"></i>Cash on Delivery
                        </label>
                        <div class="text-muted mt-1" style="font-size:13px">Pay when your order arrives.</div>
                    </div>

                    {{-- Option 3: Pay with Referral Balance --}}
                    <div class="form-check p-3 border rounded-3 payment-option-card">
                        <input class="form-check-input" type="radio" name="payment_method" value="referral" id="referral" onchange="togglePaymentDetails()">
                        <label class="form-check-label fw-bold d-flex align-items-center justify-content-between" for="referral">
                            <span><i class="fas fa-wallet me-2" style="color: #6366f1;"></i>Pay with Referral Balance</span>
                            <span class="badge bg-success-subtle text-success border border-success rounded-pill px-2 py-1" style="font-size: 12px;">
                                Available: ${{ number_format($referralBalance ?? 0, 2) }}
                            </span>
                        </label>
                        <div class="text-muted mt-1" style="font-size:13px">Use your earned referral rewards to pay for this order.</div>

                        {{-- ផ្នែកកាត់កងបូកដកលុយបង្គ្រប់ --}}
                        <div id="referral-split-box" class="mt-3 p-3 bg-light rounded-3 d-none border border-dashed">
                            @if(($referralBalance ?? 0) < $total)
                                <div class="text-warning small fw-bold mb-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Your balance is insufficient. Please choose how to pay the remaining <span class="text-danger">${{ number_format($total - ($referralBalance ?? 0), 2) }}</span>:
                                </div>
                                <div class="ms-2">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="fallback_payment_method" id="fallback_bank" value="bank_transfer" checked>
                                        <label class="form-check-label text-dark small fw-semibold" for="fallback_bank">
                                            <i class="fas fa-university me-1 text-primary"></i> Pay remaining via Bank Transfer / QR Code
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="fallback_payment_method" id="fallback_cod" value="cash_on_delivery">
                                        <label class="form-check-label text-dark small fw-semibold" for="fallback_cod">
                                            <i class="fas fa-money-bill-wave me-1 text-success"></i> Pay remaining via Cash on Delivery
                                        </label>
                                    </div>
                                </div>
                            @else
                                <div class="text-success small fw-bold mb-0">
                                    <i class="fas fa-check-circle me-1"></i> Your balance is sufficient! This order will be 100% fully covered by your referral rewards.
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            {{-- Right Column: Order Summary --}}
            <div class="col-lg-5">
                <div class="bg-white rounded-3 shadow-sm p-4 sticky-top" style="top:100px">
                    <h5 class="fw-bold mb-3">Your Order</h5>
                    
                    <div class="order-items-list mb-3" style="max-height: 250px; overflow-y: auto;">
                        @foreach($cartItems as $item)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $item->product->image) }}" style="width:50px;height:50px;object-fit:cover" onerror="this.src='{{ asset('images/default.png') }}'">
                                <span class="badge bg-primary position-absolute top-0 start-100 translate-middle rounded-pill" style="font-size:10px">{{ $item->quantity }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <div style="font-size:13px;font-weight:600">{{ Str::limit($item->product->name, 40) }}</div>
                            </div>
                            <strong>${{ number_format(($item->product->sale_price ?? $item->product->price) * $item->quantity, 2) }}</strong>
                        </div>
                        @endforeach
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong>${{ number_format($subtotal, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <strong>{{ $shippingFee > 0 ? '$'.number_format($shippingFee, 2) : 'FREE' }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <strong class="fs-5">Total</strong>
                        <strong class="fs-5 text-primary">${{ number_format($total, 2) }}</strong>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-3 fs-5">
                        Place Order →
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- ភ្ជាប់ JavaScript របស់ Leaflet Map -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
function togglePaymentDetails() {
    const referralRadio = document.getElementById('referral');
    const splitBox = document.getElementById('referral-split-box');
    
    if (referralRadio && referralRadio.checked) {
        splitBox.classList.remove('d-none');
    } else {
        splitBox.classList.add('d-none');
    }
}

document.addEventListener("DOMContentLoaded", function () {
    // ទីតាំងដើម (Default: ទីក្រុងភ្នំពេញ ក្នុងករណីមិនបើក Location)
    var defaultLat = 11.5564;
    var defaultLng = 104.9282;

    // បង្កើត Map
    var map = L.map('map').setView([defaultLat, defaultLng], 13);

    // ដាក់ Tile Layer (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // បង្កើត Marker
    var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    // 🗺️ មុខងារចាប់ទីតាំងបច្ចុប្បន្នរបស់អតិថិជន (Geolocation) ស្វ័យប្រវត្តិ
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var userLat = position.coords.latitude;
            var userLng = position.coords.longitude;

            // ផ្លាស់ទីផែនទីនិង Marker មកកាន់ទីតាំងបច្ចុប្បន្នរបស់អតិថិជន
            map.setView([userLat, userLng], 16);
            marker.setLatLng([userLat, userLng]);

            // កំណត់តម្លៃចូល Hidden Inputs
            document.getElementById('latitude').value = userLat;
            document.getElementById('longitude').value = userLng;

            // ទាញឈ្មោះទីតាំងមកដាក់ក្នុង Textarea ស្វ័យប្រវត្តិ
            fetchAddress(userLat, userLng);
        }, function (error) {
            console.log("Geolocation error or permission denied: " . error.message);
        });
    }

    // មុខងារពេលចុចលើផែនទីដោយខ្លួនឯង
    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;
        
        marker.setLatLng([lat, lng]);
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        fetchAddress(lat, lng);
    });

    // Function សម្រាប់ទាញយកឈ្មោះទីតាំង (Reverse Geocoding)
    function fetchAddress(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                if(data && data.display_name) {
                    document.getElementById('shipping_address').value = data.display_name;
                }
            }).catch(err => console.log(err));
    }
});
</script>
@endpush