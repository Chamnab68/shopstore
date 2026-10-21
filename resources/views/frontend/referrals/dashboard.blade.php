@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">

            <!-- TAB 1: Referral Program -->
            @if(request('tab', 'program') == 'program')
                <div class="card border-0 shadow-sm rounded-4 text-center p-4 mb-4" style="background: linear-gradient(135deg, #6366f1, #4f46e5); color: white;">
                    <div class="card-body">
                        <div class="mb-3"><i class="fas fa-gift fa-3x"></i></div>
                        <h2 class="fw-bold mb-2">Refer a Friend & Earn Rewards!</h2>
                        <p class="opacity-90 mb-4">Share your unique referral link with your friends and receive special rewards when they sign up and complete their purchases.</p>
                        <div class="bg-white rounded-3 p-2 d-flex align-items-center justify-content-between shadow-sm">
                            <input type="text" id="referralLink" class="form-control border-0 text-dark fw-semibold" readonly value="{{ $referralLink }}">
                            <button onclick="copyLink()" class="btn btn-dark px-4 rounded-3 text-nowrap ms-2">Copy Link</button>
                        </div>
                        <small id="copyMessage" class="text-warning mt-2 d-none fw-bold">✓ Referral link copied successfully!</small>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Friends Registered -->
                    <div class="col-6">
                        <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                            <div class="text-muted small mb-1 fw-bold">Friends Registered</div>
                            <h3 class="fw-bold text-primary mb-0">
    {{ \App\Models\User::where('referred_by', Auth::id())->count() }} Users
</h3>
                        </div>
                    </div>

                    <!-- Available Referral Balance -->
                    <div class="card">
    <div class="card-body">
        <h5>Available Referral Balance</h5>
        <!-- បង្ហាញទឹកប្រាក់ជា Dollar -->
        <h3>${{ number_format(Auth::user()->referral_cash_balance, 2) }}</h3>
    </div>
</div>
            @endif <!-- កន្លែងនេះដែលខ្វះកាលពីមុន ធ្វើឱ្យខូច Structure -->

            <!-- TAB 2: Referral History & Points Table -->
            @if(request('tab') == 'history')
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold text-dark mb-0"><i class="fas fa-history text-success me-2"></i>Referral History & Points</h4>
                        <a href="{{ url('/loyalty/referrals') }}?tab=program" class="btn btn-sm btn-outline-primary rounded-3">
                            <i class="fas fa-arrow-left me-1"></i> Back to Referral Link
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Referred User</th>
                                    <th>Registration Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(auth()->user()->referralsMade()->with('referee')->latest()->get() as $ref)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $ref->referee->name ?? 'User' }}</div>
                                            <small class="text-muted">{{ $ref->referee->email ?? '' }}</small>
                                        </td>
                                        <td>{{ $ref->created_at->format('d M Y, h:i A') }}</td>
                                        <td>
                                            @if($ref->status == 'converted')
                                                <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3">Converted</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning rounded-pill px-3">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">You have not referred any friends yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<script>
function copyLink() {
    var copyText = document.getElementById("referralLink");
    copyText.select();
    navigator.clipboard.writeText(copyText.value);
    var msg = document.getElementById("copyMessage");
    msg.classList.remove("d-none");
    setTimeout(function() { msg.classList.add("d-none"); }, 2500);
}
</script>
@endsection