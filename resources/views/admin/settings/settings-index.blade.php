@extends('layouts.admin') {{-- ពិនិត្យមើលឈ្មោះ layout របស់អ្នក --}}

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white p-3">
                    <h5 class="mb-0"><i class="fas fa-university me-2"></i>ការកំណត់ការបង់ប្រាក់ (ABA / QR Code)</h5>
                </div>
                <div class="card-body p-4">

                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- ដោយសារយើងប្រើ Route::post ក្នុង web.php ដូច្នេះមិនចាំបាច់ដាក់ @method('PUT') ទេ --}}

                        <div class="mb-3">
                            <label class="form-label fw-bold">ឈ្មោះធនាគារ</label>
                            <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" 
                                   value="{{ old('bank_name', $settings['bank_name']) }}" placeholder="ឧទាហរណ៍៖ ABA Bank">
                            @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">ឈ្មោះគណនី</label>
                            <input type="text" name="bank_account_name" class="form-control @error('bank_account_name') is-invalid @enderror" 
                                   value="{{ old('bank_account_name', $settings['bank_account_name']) }}" placeholder="ឧទាហរណ៍៖ SOK SAN">
                            @error('bank_account_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">លេខគណនី</label>
                            <input type="text" name="bank_account_number" class="form-control @error('bank_account_number') is-invalid @enderror" 
                                   value="{{ old('bank_account_number', $settings['bank_account_number']) }}" placeholder="000 111 222">
                            @error('bank_account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">រូបភាព QR Code (សម្រាប់ការបង់ប្រាក់)</label>
                            <input type="file" name="bank_qr_code" class="form-control mb-3">
                            
                            @if($settings['bank_qr_code'])
                                <div class="text-center p-3 border rounded bg-light" style="max-width: 200px;">
                                    <img src="{{ asset('storage/' . $settings['bank_qr_code']) }}" class="img-fluid rounded shadow-sm mb-2">
                                    <p class="small text-muted mb-0">QR Code បច្ចុប្បន្ន</p>
                                </div>
                            @endif
                        </div>

                        <div class="d-grid shadow-sm">
                            <button type="submit" class="btn btn-primary btn-lg">រក្សាទុកការកំណត់</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection