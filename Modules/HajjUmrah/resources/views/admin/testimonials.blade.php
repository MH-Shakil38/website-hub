@extends('admin.layouts.master')

@section('title', 'Pilgrim Testimonials — Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Pilgrim Testimonials &amp; Stories</h3>
                <span class="sb-badge-pill sb-badge-green">hajj_testimonials</span>
            </div>
            <p class="text-muted small mb-0">Manage pilgrim reviews across EN / BN / AR stored in <code>hajj_testimonials</code>.</p>
        </div>

        <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addTestiModal">
            <i class="bi bi-plus-lg me-1"></i> Add Testimonial
        </button>
    </div>

    <div class="sb-table-card">
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Pilgrim</th>
                        <th>Review (EN / BN)</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($testimonials as $t)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $t->avatar ?: '/module-media/HajjUmrah/theme/services-pilgrim.jpg' }}" alt="{{ $t->client_name }}" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $t->client_name }}</div>
                                        <div class="text-muted small">{{ $t->designation }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="max-width: 380px;">
                                <div class="small text-dark">{{ Str::limit($t->review_text, 95) }}</div>
                                <div class="text-muted small">{{ Str::limit($t->review_text_bn, 80) }}</div>
                            </td>
                            <td>
                                <span class="text-warning fw-bold"><i class="bi bi-star-fill"></i> {{ $t->rating }}/5</span>
                            </td>
                            <td>
                                <span class="sb-badge-pill {{ $t->is_active ? 'sb-badge-green' : 'sb-badge-amber' }}">
                                    {{ $t->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editTesti{{ $t->id }}"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('admin.hajjumrah.testimonials.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Delete testimonial?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modals -->
    @foreach($testimonials as $t)
        <div class="modal fade" id="editTesti{{ $t->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content bg-white border-0 shadow" style="border-radius: 16px;">
                    <form action="{{ route('admin.hajjumrah.testimonials.update', $t->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-bottom px-4 py-3">
                            <h5 class="modal-title fw-bold">Edit Testimonial: {{ $t->client_name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Pilgrim Name (EN) *</label>
                                    <input type="text" name="client_name" value="{{ $t->client_name }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Pilgrim Name (বাংলা)</label>
                                    <input type="text" name="client_name_bn" value="{{ $t->client_name_bn }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Pilgrim Name (العربية)</label>
                                    <input type="text" name="client_name_ar" value="{{ $t->client_name_ar }}" class="form-control" dir="rtl">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Designation / Group (EN)</label>
                                    <input type="text" name="designation" value="{{ $t->designation }}" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small">Rating (1-5)</label>
                                    <input type="number" name="rating" value="{{ $t->rating }}" min="1" max="5" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small">Avatar Photo</label>
                                    <input type="file" name="avatar_file" accept="image/*" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Review Text / Details (EN) *</label>
                                    <textarea name="review_text" rows="2" class="form-control sb-rich-editor sb-rte-compact" required>{{ $t->review_text }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Review Text / Details (বাংলা)</label>
                                    <textarea name="review_text_bn" rows="2" class="form-control sb-rich-editor sb-rte-compact">{{ $t->review_text_bn }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Review Text / Details (العربية)</label>
                                    <textarea name="review_text_ar" rows="2" class="form-control sb-rich-editor sb-rte-compact" dir="rtl">{{ $t->review_text_ar }}</textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $t->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success fw-bold">Update Testimonial</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Modal -->
    <div class="modal fade" id="addTestiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius: 16px;">
                <form action="{{ route('admin.hajjumrah.testimonials.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Add Pilgrim Testimonial</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Pilgrim Name (EN) *</label>
                                <input type="text" name="client_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Pilgrim Name (বাংলা)</label>
                                <input type="text" name="client_name_bn" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Pilgrim Name (العربية)</label>
                                <input type="text" name="client_name_ar" class="form-control" dir="rtl">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Designation / Group</label>
                                <input type="text" name="designation" class="form-control" placeholder="Standard Hajj Pilgrim, Dhaka">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">Rating</label>
                                <input type="number" name="rating" value="5" min="1" max="5" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">Photo</label>
                                <input type="file" name="avatar_file" accept="image/*" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Review Text / Details (EN) *</label>
                                <textarea name="review_text" rows="2" class="form-control sb-rich-editor sb-rte-compact" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Review Text / Details (বাংলা)</label>
                                <textarea name="review_text_bn" rows="2" class="form-control sb-rich-editor sb-rte-compact"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Review Text / Details (العربية)</label>
                                <textarea name="review_text_ar" rows="2" class="form-control sb-rich-editor sb-rte-compact" dir="rtl"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold">Save Testimonial</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
