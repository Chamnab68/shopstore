@extends('layouts.app')

@section('content')
<div class="container py-5">
    
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h2 class="fw-bold text-dark">🎁 Rewards Catalog</h2>
            <p class="text-muted">ប្រើប្រាស់ពិន្ទុសន្សំរបស់អ្នកដើម្បីប្តូរយករង្វាន់ និងគូប៉ុងបញ្ចុះតម្លៃពិសេសៗ</p>
            <div class="d-inline-flex align-items-center bg-warning bg-opacity-10 text-warning px-4 py-2 rounded-pill border border-warning border-opacity-25 shadow-sm mt-2">
                <i class="fas fa-coins me-2 fa-lg"></i>
                <span class="fw-bold fs-5 text-dark">{{ number_format(auth()->user()->points ?? 0) }}</span>
                <span class="text-muted ms-1 small">Available Points</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h4 class="fw-bold mb-4 text-dark"><i class="fas fa-gift text-primary me-2"></i> Available Gifts</h4>
    <div class="row g-4">
        @php
            // 🟢 ដោះស្រាយការពារ៖ បើសិនជា Controller ភ្លេចបោះ variable $rewards មក វានឹងទាញយកស្វ័យប្រវត្តិតែម្តង
            $rewardsList = $rewards ?? \App\Models\Reward::where('is_active', true)->latest()->get();
        @endphp

        @forelse($rewardsList as $reward)
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative border">
                    <img src="{{ $reward->image ? asset('storage/' . $reward->image) : 'https://placehold.co/300x200?text=' . urlencode($reward->name) }}" class="card-img-top" alt="{{ $reward->name }}" style="height: 180px; object-fit: cover;">
                    
                    <span class="position-absolute top-0 end-0 bg-dark text-white fw-bold px-3 py-1 m-3 rounded-pill small shadow-sm">
                        {{ number_format($reward->point_cost) }} Pts
                    </span>

                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title fw-bold text-dark mb-2" style="font-size: 16px;">{{ $reward->name }}</h5>
                        <p class="card-text text-muted small flex-grow-1 mb-1">{{ Str::limit($reward->description ?? 'No description available.', 60) }}</p>
                        <div class="mb-3 small text-secondary">
                            <i class="fas fa-box me-1"></i> Stock: <strong>{{ $reward->total_stock }}</strong> remaining
                        </div>
                        
                        @if(auth()->user()->points >= $reward->point_cost && $reward->total_stock > 0)
                            <form action="{{ route('rewards.redeem', $reward->id) }}" method="POST"> 
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold btn-sm py-2" onclick="return confirm('តើអ្នកពិតជាចង់ប្រើប្រាស់ពិន្ទុដើម្បីប្តូរយករង្វាន់នេះមែនទេ?')">
                                    Redeem Now
                                </button>
                            </form>
                        @elseif($reward->total_stock <= 0)
                            <button type="button" class="btn btn-secondary w-100 rounded-pill fw-bold btn-sm py-2" disabled>
                                Out of Stock
                            </button>
                        @else
                            <button type="button" class="btn btn-light w-100 rounded-pill text-muted btn-sm py-2" disabled>
                                Points Insufficient
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">មិនទាន់មានទំនិញរង្វាន់សកម្មនៅក្នុងប្រព័ន្ធនៅឡើយទេ។</p>
            </div>
        @endforelse
    </div>

    <div class="card border-0 shadow-sm rounded-4 mt-5 border">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-history text-muted me-2"></i> My Claimed Rewards</h5>
        </div>
        <div class="card-body px-4 pb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Redeem Code</th>
                            <th>Reward Item</th>
                            <th>Points Used</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // 🟢 ទាញយកប្រវត្តិកាដូដែលបានដូររួចរបស់ User ម្នាក់នេះមកបង្ហាញ
                            $myRedemptionsList = $myRedemptions ?? \App\Models\PointsTransaction::where('user_id', auth()->id())->where('type', 'redeem')->latest()->get();
                        @endphp

                        @forelse($myRedemptionsList as $redeem)
                        <tr>
                            <td class="text-muted small">{{ \Carbon\Carbon::parse($redeem->created_at)->format('d M Y, h:i A') }}</td>
                            <td><code class="fw-bold text-primary">{{ $redeem->redemption_code ?? 'RC-'.str_pad($redeem->id, 6, '0', STR_PAD_LEFT) }}</code></td>
                            <td><span class="fw-medium text-dark">{{ $redeem->reward_name ?? ($redeem->reward ? $redeem->reward->name : 'Reward Item') }}</span></td>
                            <td class="fw-bold text-danger">-{{ number_format(abs($redeem->points)) }} Pts</td>
                            <td>
                                @if(($redeem->status ?? 'completed') == 'pending')
                                    <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill small">Pending Approval</span>
                                @else
                                    <span class="badge bg-success text-white px-3 py-1.5 rounded-pill small">Fulfilled / Delivered</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">អ្នកមិនទាន់ធ្លាប់ធ្វើការដោះដូរយករង្វាន់ណាមួយនៅឡើយទេ។</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection