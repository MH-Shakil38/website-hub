@extends('admin.layouts.master')

@section('title', 'Client Testimonials CRUD — Developer Portfolio')

@section('admin_content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 style="font-size: 23px; font-weight: 800; color: #0F172A; margin: 0;">Client Testimonials</h3>
            <p class="text-muted small mb-0">Manage client reviews, photos, designations, and star ratings stored in <code>port_testimonials</code>.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#addTestiModal">
            <i class="bi bi-plus-lg me-1"></i> Add Testimonial
        </button>
    </div>

    <div class="sb-table-card">
        <table class="sb-data-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Review Text</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($testimonials as $testi)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $testi->avatar ?: '/module-media/Portfolio/theme/avatar-1.jpg' }}" alt="{{ $testi->client_name }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark">{{ $testi->client_name }}</div>
                                    <small class="text-muted">{{ $testi->designation }}, {{ $testi->company }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="max-width: 440px;">{{ $testi->review_text }}</td>
                        <td><span class="text-warning fw-bold">{{ $testi->rating }} ★</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editTesti{{ $testi->id }}"><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('admin.portfolio.testimonials.destroy', $testi->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="editTesti{{ $testi->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <form action="{{ route('admin.portfolio.testimonials.update', $testi->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow" style="border-radius: 16px;">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit Testimonial</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Client Name *</label>
                                            <input type="text" name="client_name" value="{{ $testi->client_name }}" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Designation</label>
                                            <input type="text" name="designation" value="{{ $testi->designation }}" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Company</label>
                                            <input type="text" name="company" value="{{ $testi->company }}" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Star Rating (1-5)</label>
                                            <input type="number" name="rating" value="{{ $testi->rating }}" min="1" max="5" class="form-control">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold small">Review Text *</label>
                                            <textarea name="review_text" rows="3" class="form-control" required>{{ $testi->review_text }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold small">Client Photo</label>
                                            <input type="file" name="avatar_file" accept="image/*" class="form-control">
                                        </div>
                                        <input type="hidden" name="is_active" value="1">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary fw-bold">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="addTestiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.portfolio.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Client Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Client Name *</label>
                            <input type="text" name="client_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Designation</label>
                            <input type="text" name="designation" class="form-control" placeholder="CEO">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Company</label>
                            <input type="text" name="company" class="form-control" placeholder="DreamTech">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Rating (1-5)</label>
                            <input type="number" name="rating" value="5" min="1" max="5" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Review Text *</label>
                            <textarea name="review_text" rows="3" class="form-control" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Client Photo</label>
                            <input type="file" name="avatar_file" accept="image/*" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary fw-bold">Add Testimonial</button>
                </div>
            </form>
        </div>
    </div>
@endsection
