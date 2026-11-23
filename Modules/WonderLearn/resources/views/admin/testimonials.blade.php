@extends('admin.layouts.master')

@section('title', 'Parent Testimonials - WonderLearn Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="color: #0B2545;">Loved by Parents Testimonials (wl_testimonials)</h3>
        <p class="text-muted small mb-0">Manage parent reviews and ratings shown on the homepage.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addTestiModal">
        <i class="bi bi-plus-lg me-1"></i> Add Parent Review
    </button>
</div>

<div class="row g-3">
    @foreach($testimonials as $testi)
        <div class="col-md-4">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ $testi->avatar }}" alt="{{ $testi->parent_name }}" style="width: 52px; height: 52px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <h6 class="fw-bold mb-0">{{ $testi->parent_name }}</h6>
                        <div class="text-warning small">
                            @for($i = 1; $i <= ($testi->rating ?: 5); $i++) ★ @endfor
                        </div>
                    </div>
                </div>
                <form action="{{ route('admin.wonderlearn.testimonials.update', $testi->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Parent Name</label>
                        <input type="text" name="parent_name" value="{{ $testi->parent_name }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Review Quote</label>
                        <textarea name="quote" rows="3" class="form-control form-control-sm" required>{{ $testi->quote }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold mb-1">Replace Photo</label>
                        <input type="file" name="avatar_file" class="form-control form-control-sm" accept="image/*">
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-sm btn-primary rounded-3 fw-semibold">Update</button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                onclick="if(confirm('Delete this review?')) document.getElementById('del-testi-{{ $testi->id }}').submit();">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </form>
                <form id="del-testi-{{ $testi->id }}" action="{{ route('admin.wonderlearn.testimonials.destroy', $testi->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Testimonial Modal -->
<div class="modal fade" id="addTestiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.wonderlearn.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Parent Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Parent Name *</label>
                    <input type="text" name="parent_name" class="form-control" required placeholder="e.g. Sarah M.">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Quote *</label>
                    <textarea name="quote" rows="3" class="form-control" required></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Parent Photo</label>
                    <input type="file" name="avatar_file" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded-3 fw-semibold">Save Review</button>
            </div>
        </form>
    </div>
</div>
@endsection
