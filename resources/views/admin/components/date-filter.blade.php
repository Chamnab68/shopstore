<div class="card mb-4">
    <div class="card-body">
        <form action="" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="from_date" class="form-label fw-bold text-muted small">From Date</label>
                <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-4">
                <label for="to_date" class="form-label fw-bold text-muted small">To Date</label>
                <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100" style="background: var(--primary);">
                    <i class="fas fa-filter me-1"></i> Apply Filter
                </button>
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary" title="Reset Filter">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>
</div>