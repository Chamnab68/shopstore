@extends('layouts.admin')
@section('title','Categories')
@section('page-title','Category Management')

@section('content')
<div class="row g-4">
    {{-- Add Category Form --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Add New Category</h6>
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:13px">Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:13px">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:13px">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:13px">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label fw-bold" style="font-size:13px">Active</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Add Category</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Categories List --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">All Categories ({{ $categories->total() }})</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Products</th>
                                <th>Sort</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($categories as $cat)
                        <tr id="cat-row-{{ $cat->id }}">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:40px;height:40px;border-radius:8px;background:#f8f9fa;border:1px solid #eee;display:flex;align-items:center;justify-content:center;overflow:hidden">
                                        @if($cat->image)
                                        <img src="{{ asset('storage/'.$cat->image) }}" style="width:100%;height:100%;object-fit:cover">
                                        @else
                                        <i class="fas fa-tags text-muted"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="font-size:14px">{{ $cat->name }}</div>
                                        <div class="text-muted" style="font-size:11px">{{ $cat->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $cat->products_count }} products</span></td>
                            <td>{{ $cat->sort_order }}</td>
                            <td>
                                @if($cat->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editCategory({{ $cat->id }},'{{ $cat->name }}','{{ $cat->sort_order }}',{{ $cat->is_active ? 1:0 }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('admin.categories.destroy',$cat->id) }}','Delete this category?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No categories yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit-form" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Name *</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sort Order</label>
                        <input type="number" name="sort_order" id="edit-sort" class="form-control">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="edit-active" value="1">
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editCategory(id, name, sort, active) {
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-sort').value = sort;
    document.getElementById('edit-active').checked = active;
    document.getElementById('edit-form').action = `/admin/categories/${id}`;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>
@endpush
