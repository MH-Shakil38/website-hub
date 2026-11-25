@extends('admin.layouts.master')

@section('title', 'Affiliated Airlines, Hotels & Trust Badges — Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Affiliated Airlines, Hotels &amp; Review Badges</h3>
                <span class="sb-badge-pill sb-badge-green">hajj_partners</span>
            </div>
            <p class="text-muted small mb-0">Manage Saudia, Turkish Airlines, Qatar Airways, Singapore Airlines, Hilton, Hyatt, and Trustpilot/Facebook badges in <code>hajj_partners</code>.</p>
        </div>

        <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addPartnerModal">
            <i class="bi bi-plus-lg me-1"></i> Add Partner / Badge
        </button>
    </div>

    <div class="sb-table-card">
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Partner / Brand</th>
                        <th>Category</th>
                        <th>Rating / Reviews (If TrustBadge)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($partners as $p)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($p->logo)
                                        <img src="{{ $p->logo }}" alt="{{ $p->name }}" style="height: 32px; width: auto;">
                                    @else
                                        <span class="rounded-2 d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: #F1F5F9; color: {{ $p->brand_color ?: '#036939' }};">
                                            <i class="{{ $p->icon_class ?: 'fa-solid fa-plane' }}"></i>
                                        </span>
                                    @endif
                                    <span class="fw-bold text-dark">{{ $p->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="sb-badge-pill {{ $p->partner_type === 'TrustBadge' ? 'sb-badge-amber' : ($p->partner_type === 'Airline' ? 'sb-badge-green' : 'sb-badge-blue') }}">
                                    {{ $p->partner_type }}
                                </span>
                            </td>
                            <td class="small">{{ $p->rating_text ? $p->rating_text . ' (' . $p->reviews_count . ')' : '—' }}</td>
                            <td>
                                <span class="sb-badge-pill {{ $p->is_active ? 'sb-badge-green' : 'sb-badge-amber' }}">
                                    {{ $p->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editPartner{{ $p->id }}"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('admin.hajjumrah.partners.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Remove {{ $p->name }}?')">
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
    @foreach($partners as $p)
        <div class="modal fade" id="editPartner{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-white border-0 shadow" style="border-radius: 16px;">
                    <form action="{{ route('admin.hajjumrah.partners.update', $p->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-bottom px-4 py-3">
                            <h5 class="modal-title fw-bold">Edit Partner: {{ $p->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Brand Name *</label>
                                    <input type="text" name="name" value="{{ $p->name }}" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Type *</label>
                                    <select name="partner_type" class="form-select">
                                        <option value="Airline" {{ $p->partner_type === 'Airline' ? 'selected' : '' }}>Airline</option>
                                        <option value="Hotel" {{ $p->partner_type === 'Hotel' ? 'selected' : '' }}>Hotel</option>
                                        <option value="TrustBadge" {{ $p->partner_type === 'TrustBadge' ? 'selected' : '' }}>Trust Badge</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Rating Text (e.g. 4.5/5)</label>
                                    <input type="text" name="rating_text" value="{{ $p->rating_text }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Reviews Count (e.g. 1597 reviews)</label>
                                    <input type="text" name="reviews_count" value="{{ $p->reviews_count }}" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Upload Custom Logo Image (Optional)</label>
                                    <input type="file" name="logo_file" accept="image/*" class="form-control">
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $p->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success fw-bold">Update Partner</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Partner Modal -->
    <div class="modal fade" id="addPartnerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius: 16px;">
                <form action="{{ route('admin.hajjumrah.partners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Add Airline, Hotel or Review Badge</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Brand Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Emirates / Marriott" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Type *</label>
                                <select name="partner_type" class="form-select">
                                    <option value="Airline">Airline</option>
                                    <option value="Hotel">Hotel</option>
                                    <option value="TrustBadge">Trust Badge</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Rating (Optional)</label>
                                <input type="text" name="rating_text" class="form-control" placeholder="5/5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Reviews (Optional)</label>
                                <input type="text" name="reviews_count" class="form-control" placeholder="1200 reviews">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Logo Image (Optional)</label>
                                <input type="file" name="logo_file" accept="image/*" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold">Save Partner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
