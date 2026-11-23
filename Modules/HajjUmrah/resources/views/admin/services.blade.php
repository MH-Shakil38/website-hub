@extends('admin.layouts.master')

@section('title', 'What Do We Offer (Services) — Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">What Do We Offer (Services Management)</h3>
                <span class="sb-badge-pill sb-badge-green">hajj_services</span>
            </div>
            <p class="text-muted small mb-0">Manage Air Ticketing, Hotel Booking, Transport Services, Tour Guide, Food Services &amp; 24/7 Support across EN / BN / AR in <code>hajj_services</code>.</p>
        </div>

        <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addServiceModal">
            <i class="bi bi-plus-lg me-1"></i> Add Service
        </button>
    </div>

    <div class="sb-table-card">
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Service Title (EN / BN / AR)</th>
                        <th>Subtitle (EN / BN / AR)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $srv)
                        <tr>
                            <td>
                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: #E3F5EA; color: #1FA84F; font-size: 18px;">
                                    <i class="{{ $srv->icon_class ?: 'fa-solid fa-plane-up' }}"></i>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $srv->title }}</div>
                                <div class="text-muted small">{{ $srv->title_bn }} • <span dir="rtl">{{ $srv->title_ar }}</span></div>
                            </td>
                            <td>
                                <div class="small text-dark">{{ $srv->subtitle }}</div>
                                <div class="text-muted small">{{ $srv->subtitle_bn }} • <span dir="rtl">{{ $srv->subtitle_ar }}</span></div>
                            </td>
                            <td>
                                <span class="sb-badge-pill {{ $srv->is_active ? 'sb-badge-green' : 'sb-badge-amber' }}">
                                    {{ $srv->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editSrv{{ $srv->id }}"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('admin.hajjumrah.services.destroy', $srv->id) }}" method="POST" onsubmit="return confirm('Delete service {{ $srv->title }}?')">
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
    @foreach($services as $srv)
        <div class="modal fade" id="editSrv{{ $srv->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content bg-white border-0 shadow" style="border-radius: 16px;">
                    <form action="{{ route('admin.hajjumrah.services.update', $srv->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-bottom px-4 py-3">
                            <h5 class="modal-title fw-bold">Edit Service: {{ $srv->title }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Title (EN) *</label>
                                    <input type="text" name="title" value="{{ $srv->title }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Title (বাংলা)</label>
                                    <input type="text" name="title_bn" value="{{ $srv->title_bn }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Title (العربية)</label>
                                    <input type="text" name="title_ar" value="{{ $srv->title_ar }}" class="form-control" dir="rtl">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Subtitle (EN)</label>
                                    <input type="text" name="subtitle" value="{{ $srv->subtitle }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Subtitle (বাংলা)</label>
                                    <input type="text" name="subtitle_bn" value="{{ $srv->subtitle_bn }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Subtitle (العربية)</label>
                                    <input type="text" name="subtitle_ar" value="{{ $srv->subtitle_ar }}" class="form-control" dir="rtl">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Service Details / Description (EN)</label>
                                    <textarea name="description" rows="3" class="form-control sb-rich-editor sb-rte-compact">{{ $srv->description }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Service Details / Description (বাংলা)</label>
                                    <textarea name="description_bn" rows="3" class="form-control sb-rich-editor sb-rte-compact">{{ $srv->description_bn }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Service Details / Description (العربية)</label>
                                    <textarea name="description_ar" rows="3" class="form-control sb-rich-editor sb-rte-compact" dir="rtl">{{ $srv->description_ar }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">FontAwesome Icon Class</label>
                                    <input type="text" name="icon_class" value="{{ $srv->icon_class }}" class="form-control">
                                </div>
                                <div class="col-md-6 d-flex align-items-center pt-3">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $srv->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success fw-bold">Update Service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius: 16px;">
                <form action="{{ route('admin.hajjumrah.services.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Add New Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Title (EN) *</label>
                                <input type="text" name="title" class="form-control" placeholder="Visa Processing" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Title (বাংলা)</label>
                                <input type="text" name="title_bn" class="form-control" placeholder="ভিসা প্রসেসিং">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Title (العربية)</label>
                                <input type="text" name="title_ar" class="form-control" dir="rtl" placeholder="تجهيز التأشيرات">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Subtitle (EN)</label>
                                <input type="text" name="subtitle" class="form-control" placeholder="Fast E-Visa & Biometrics">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Icon Class</label>
                                <input type="text" name="icon_class" class="form-control" value="fa-solid fa-passport">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Service Details / Description (EN)</label>
                                <textarea name="description" rows="3" class="form-control sb-rich-editor sb-rte-compact"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Service Details / Description (বাংলা)</label>
                                <textarea name="description_bn" rows="3" class="form-control sb-rich-editor sb-rte-compact"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Service Details / Description (العربية)</label>
                                <textarea name="description_ar" rows="3" class="form-control sb-rich-editor sb-rte-compact" dir="rtl"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold">Save Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
