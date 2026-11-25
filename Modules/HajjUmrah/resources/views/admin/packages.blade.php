@extends('admin.layouts.master')

@section('title', 'Special Offers & Hajj / Umrah Packages — Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Special Offers &amp; Hajj / Umrah Packages</h3>
                <span class="sb-badge-pill sb-badge-green">hajj_packages</span>
            </div>
            <p class="text-muted small mb-0">Manage Special Offer cards, Hajj &amp; Umrah packages, prices, durations, and translations (EN / BN / AR) stored in <code>hajj_packages</code>.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('admin.hajjumrah.packages.index') }}" method="GET" class="d-flex gap-2">
                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 150px;">
                    <option value="">All Types</option>
                    <option value="Hajj" {{ $type === 'Hajj' ? 'selected' : '' }}>Hajj Packages</option>
                    <option value="Umrah" {{ $type === 'Umrah' ? 'selected' : '' }}>Umrah Packages</option>
                    <option value="Tour" {{ $type === 'Tour' ? 'selected' : '' }}>Ziyarah / Tour</option>
                </select>
                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search packages..." style="width: 180px;">
            </form>
            <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addPackageModal">
                <i class="bi bi-plus-lg me-1"></i> Add Package
            </button>
        </div>
    </div>

    <div class="sb-table-card">
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Package (EN / BN / AR)</th>
                        <th>Type</th>
                        <th>Starting Price</th>
                        <th>Duration</th>
                        <th>Special Offer</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($packages as $pkg)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $pkg->thumbnail }}" alt="{{ $pkg->title }}" style="width: 64px; height: 46px; object-fit: cover; border-radius: 8px; border: 1px solid #E2E8F0;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $pkg->title }}</div>
                                        <div class="text-muted small">{{ $pkg->title_bn }} • <span dir="rtl">{{ $pkg->title_ar }}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="sb-badge-pill {{ $pkg->package_type === 'Hajj' ? 'sb-badge-green' : 'sb-badge-blue' }}">{{ $pkg->package_type }}</span></td>
                            <td><strong class="text-primary">{{ $pkg->price_display }}</strong> <span class="text-muted small">{{ $pkg->price_unit }}</span></td>
                            <td class="small">{{ $pkg->duration }}</td>
                            <td>
                                @if($pkg->is_special_offer)
                                    <span class="sb-badge-pill sb-badge-amber">Special Offer</span>
                                @else
                                    <span class="text-muted small">Regular</span>
                                @endif
                            </td>
                            <td>
                                <span class="sb-badge-pill {{ $pkg->is_active ? 'sb-badge-green' : 'sb-badge-amber' }}">
                                    {{ $pkg->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('hajjumrah.package.show', $pkg->slug) }}" target="_blank" class="sb-page-btn text-primary" title="View Package"><i class="bi bi-eye"></i></a>
                                    <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editPkg{{ $pkg->id }}" title="Edit"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('admin.hajjumrah.packages.destroy', $pkg->id) }}" method="POST" onsubmit="return confirm('Delete package {{ $pkg->title }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sb-page-btn text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $packages->links() }}</div>
    </div>

    <!-- Edit Package Modals -->
    @foreach($packages as $pkg)
        <div class="modal fade" id="editPkg{{ $pkg->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content bg-white border-0 shadow" style="border-radius: 16px;">
                    <form action="{{ route('admin.hajjumrah.packages.update', $pkg->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-bottom px-4 py-3">
                            <h5 class="modal-title fw-bold">Edit Package: {{ $pkg->title }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Package Type *</label>
                                    <select name="package_type" class="form-select">
                                        <option value="Hajj" {{ $pkg->package_type === 'Hajj' ? 'selected' : '' }}>Hajj</option>
                                        <option value="Umrah" {{ $pkg->package_type === 'Umrah' ? 'selected' : '' }}>Umrah</option>
                                        <option value="Tour" {{ $pkg->package_type === 'Tour' ? 'selected' : '' }}>Tour</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Starting Price *</label>
                                    <input type="text" name="price_display" value="{{ $pkg->price_display }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Duration (EN) *</label>
                                    <input type="text" name="duration" value="{{ $pkg->duration }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Package Name (EN) *</label>
                                    <input type="text" name="title" value="{{ $pkg->title }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Package Name (বাংলা)</label>
                                    <input type="text" name="title_bn" value="{{ $pkg->title_bn }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Package Name (العربية)</label>
                                    <input type="text" name="title_ar" value="{{ $pkg->title_ar }}" class="form-control" dir="rtl">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Duration (বাংলা)</label>
                                    <input type="text" name="duration_bn" value="{{ $pkg->duration_bn }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Duration (العربية)</label>
                                    <input type="text" name="duration_ar" value="{{ $pkg->duration_ar }}" class="form-control" dir="rtl">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Flight Info</label>
                                    <input type="text" name="flight_info" value="{{ $pkg->flight_info }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Makkah Hotel</label>
                                    <input type="text" name="makkah_hotel" value="{{ $pkg->makkah_hotel }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Madinah Hotel</label>
                                    <input type="text" name="madinah_hotel" value="{{ $pkg->madinah_hotel }}" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Short Description (EN)</label>
                                    <textarea name="short_description" rows="2" class="form-control sb-rich-editor sb-rte-compact">{{ $pkg->short_description }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Short Description (বাংলা)</label>
                                    <textarea name="short_description_bn" rows="2" class="form-control sb-rich-editor sb-rte-compact">{{ $pkg->short_description_bn }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Short Description (العربية)</label>
                                    <textarea name="short_description_ar" rows="2" class="form-control sb-rich-editor sb-rte-compact" dir="rtl">{{ $pkg->short_description_ar }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Full Package Details &amp; Itinerary (EN)</label>
                                    <textarea name="description" rows="4" class="form-control sb-rich-editor">{{ $pkg->description }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Full Package Details &amp; Itinerary (বাংলা)</label>
                                    <textarea name="description_bn" rows="4" class="form-control sb-rich-editor">{{ $pkg->description_bn }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Full Package Details &amp; Itinerary (العربية)</label>
                                    <textarea name="description_ar" rows="4" class="form-control sb-rich-editor" dir="rtl">{{ $pkg->description_ar }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Features (Comma Separated)</label>
                                    <input type="text" name="features_raw" value="{{ implode(', ', $pkg->features ?? []) }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Replace Package Image</label>
                                    <input type="file" name="thumbnail_file" accept="image/*" class="form-control">
                                </div>
                                <div class="col-md-6 d-flex align-items-center gap-4 pt-2">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_special_offer" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_special_offer" value="1" {{ $pkg->is_special_offer ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold">Show in Special Offers</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $pkg->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success fw-bold">Update Package</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Package Modal -->
    <div class="modal fade" id="addPackageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius: 16px;">
                <form action="{{ route('admin.hajjumrah.packages.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Add New Hajj / Umrah Package</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Package Type *</label>
                                <select name="package_type" class="form-select">
                                    <option value="Hajj">Hajj</option>
                                    <option value="Umrah">Umrah</option>
                                    <option value="Tour">Tour</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Starting Price (e.g. $12,000) *</label>
                                <input type="text" name="price_display" class="form-control" placeholder="$12,000" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Duration (EN) *</label>
                                <input type="text" name="duration" class="form-control" placeholder="Duration : (15–25 Days)" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Package Name (EN) *</label>
                                <input type="text" name="title" class="form-control" placeholder="Hajj Package" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Package Name (বাংলা)</label>
                                <input type="text" name="title_bn" class="form-control" placeholder="হজ্জ প্যাকেজ">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Package Name (العربية)</label>
                                <input type="text" name="title_ar" class="form-control" dir="rtl" placeholder="باقة الحج">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Short Description (EN)</label>
                                <textarea name="short_description" rows="2" class="form-control sb-rich-editor sb-rte-compact"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Short Description (বাংলা)</label>
                                <textarea name="short_description_bn" rows="2" class="form-control sb-rich-editor sb-rte-compact"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Short Description (العربية)</label>
                                <textarea name="short_description_ar" rows="2" class="form-control sb-rich-editor sb-rte-compact" dir="rtl"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Full Package Details &amp; Itinerary (EN)</label>
                                <textarea name="description" rows="4" class="form-control sb-rich-editor"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Full Package Details &amp; Itinerary (বাংলা)</label>
                                <textarea name="description_bn" rows="4" class="form-control sb-rich-editor"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Full Package Details &amp; Itinerary (العربية)</label>
                                <textarea name="description_ar" rows="4" class="form-control sb-rich-editor" dir="rtl"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Features (Comma Separated)</label>
                                <input type="text" name="features_raw" class="form-control" value="Direct Flight, Visa Processing, 5-Star Hotel, Ziyarah Tour">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Package Thumbnail Image</label>
                                <input type="file" name="thumbnail_file" accept="image/*" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold">Create Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
