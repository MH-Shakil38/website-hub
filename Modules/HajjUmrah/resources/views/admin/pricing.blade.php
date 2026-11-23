@extends('admin.layouts.master')

@section('title', 'All-Inclusive Package Pricing Plans — Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">All-Inclusive Package Pricing (Economy / Standard / Classic)</h3>
                <span class="sb-badge-pill sb-badge-green">hajj_pricing_plans</span>
            </div>
            <p class="text-muted small mb-0">Manage the 3 dedicated package pricing cards (BDT 6,35,000 / 7,35,000 / 8,85,000) with hotel distances, flights, food &amp; translations in <code>hajj_pricing_plans</code>.</p>
        </div>

        <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addPricingModal">
            <i class="bi bi-plus-lg me-1"></i> Add Pricing Card
        </button>
    </div>

    <div class="row g-4">
        @foreach($pricingPlans as $plan)
            <div class="col-lg-4">
                <div class="p-4 rounded-4 border shadow-sm h-100 d-flex flex-column {{ $plan->is_highlighted ? 'text-white' : 'bg-white' }}"
                     style="{{ $plan->is_highlighted ? 'background: linear-gradient(180deg, #04543C 0%, #022C1F 100%);' : '' }}">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge {{ $plan->is_highlighted ? 'bg-warning text-dark' : 'bg-success' }}">
                            {{ $plan->is_highlighted ? 'Highlighted Dark Green Card' : 'Clean White Card' }}
                        </span>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#editPricing{{ $plan->id }}"><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('admin.hajjumrah.pricing.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Delete pricing plan {{ $plan->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>

                    <h4 class="fw-bold mb-1">{{ $plan->name }}</h4>
                    <div class="small opacity-75 mb-3">{{ $plan->name_bn }} • <span dir="rtl">{{ $plan->name_ar }}</span></div>

                    <div class="mb-3">
                        <span class="small fw-bold">{{ $plan->currency }}</span>
                        <span class="fs-2 fw-bold text-success">{{ $plan->price }}</span>
                        <small class="ms-1">{{ $plan->starts_from_label }}</small>
                    </div>

                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0 flex-grow-1">
                        <li><i class="bi bi-clock-fill text-success me-2"></i> {{ $plan->duration }}</li>
                        <li><i class="bi bi-airplane-fill text-success me-2"></i> {{ $plan->direct_flight }}</li>
                        <li><i class="bi bi-building-fill text-success me-2"></i> {{ $plan->hotel_aziziyah }}</li>
                        <li><i class="bi bi-geo-alt-fill text-success me-2"></i> {{ $plan->hotel_makkah }}</li>
                        <li><i class="bi bi-geo-fill text-success me-2"></i> {{ $plan->hotel_madinah }}</li>
                        <li><i class="bi bi-cup-hot-fill text-success me-2"></i> {{ $plan->food_service }}</li>
                        <li><i class="bi bi-bus-front-fill text-success me-2"></i> {{ $plan->special_services }}</li>
                    </ul>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Edit Pricing Modals -->
    @foreach($pricingPlans as $plan)
        <div class="modal fade" id="editPricing{{ $plan->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content bg-white border-0 shadow" style="border-radius: 16px;">
                    <form action="{{ route('admin.hajjumrah.pricing.update', $plan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-bottom px-4 py-3">
                            <h5 class="modal-title fw-bold">Edit Pricing Card: {{ $plan->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small">Package Title (EN) *</label>
                                    <input type="text" name="name" value="{{ $plan->name }}" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small">Package Title (বাংলা)</label>
                                    <input type="text" name="name_bn" value="{{ $plan->name_bn }}" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small">Package Title (العربية)</label>
                                    <input type="text" name="name_ar" value="{{ $plan->name_ar }}" class="form-control" dir="rtl">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small">Starting Price *</label>
                                    <div class="input-group">
                                        <input type="text" name="currency" value="{{ $plan->currency }}" class="form-control" style="max-width: 75px;">
                                        <input type="text" name="price" value="{{ $plan->price }}" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Package Description / Tagline (EN)</label>
                                    <textarea name="tagline" rows="2" class="form-control sb-rich-editor sb-rte-compact">{{ $plan->tagline }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Package Description / Tagline (বাংলা)</label>
                                    <textarea name="tagline_bn" rows="2" class="form-control sb-rich-editor sb-rte-compact">{{ $plan->tagline_bn }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Package Description / Tagline (العربية)</label>
                                    <textarea name="tagline_ar" rows="2" class="form-control sb-rich-editor sb-rte-compact" dir="rtl">{{ $plan->tagline_ar }}</textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Duration (EN) *</label>
                                    <input type="text" name="duration" value="{{ $plan->duration }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Duration (বাংলা)</label>
                                    <input type="text" name="duration_bn" value="{{ $plan->duration_bn }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Duration (العربية)</label>
                                    <input type="text" name="duration_ar" value="{{ $plan->duration_ar }}" class="form-control" dir="rtl">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Direct Flight (EN) *</label>
                                    <input type="text" name="direct_flight" value="{{ $plan->direct_flight }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Direct Flight (বাংলা)</label>
                                    <input type="text" name="direct_flight_bn" value="{{ $plan->direct_flight_bn }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Direct Flight (العربية)</label>
                                    <input type="text" name="direct_flight_ar" value="{{ $plan->direct_flight_ar }}" class="form-control" dir="rtl">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Hotel Aziziyah (EN) *</label>
                                    <input type="text" name="hotel_aziziyah" value="{{ $plan->hotel_aziziyah }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Hotel Makkah (EN) *</label>
                                    <input type="text" name="hotel_makkah" value="{{ $plan->hotel_makkah }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Hotel Madinah (EN) *</label>
                                    <input type="text" name="hotel_madinah" value="{{ $plan->hotel_madinah }}" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Food Service (EN) *</label>
                                    <input type="text" name="food_service" value="{{ $plan->food_service }}" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Special Services (EN) *</label>
                                    <input type="text" name="special_services" value="{{ $plan->special_services }}" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Icon Class</label>
                                    <input type="text" name="icon_class" value="{{ $plan->icon_class }}" class="form-control">
                                </div>
                                <div class="col-md-8 d-flex align-items-center gap-4 pt-3">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_highlighted" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_highlighted" value="1" {{ $plan->is_highlighted ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold">Highlight with Dark Emerald Green Background (Standard Style)</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success fw-bold">Save Pricing Card</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Pricing Modal -->
    <div class="modal fade" id="addPricingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius: 16px;">
                <form action="{{ route('admin.hajjumrah.pricing.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Add New Pricing Card</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Package Title (EN) *</label>
                                <input type="text" name="name" class="form-control" placeholder="VIP Package" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Currency</label>
                                <input type="text" name="currency" class="form-control" value="BDT">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Starting Price *</label>
                                <input type="text" name="price" class="form-control" placeholder="9,50,000" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Duration *</label>
                                <input type="text" name="duration" class="form-control" value="Duration: 40/42 Days" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Direct Flight *</label>
                                <input type="text" name="direct_flight" class="form-control" value="Direct Flight: SA/Biman/Flynas" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Hotel Aziziyah *</label>
                                <input type="text" name="hotel_aziziyah" class="form-control" value="Hotel Aziziyah: Near to Jamarah" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Hotel Makkah *</label>
                                <input type="text" name="hotel_makkah" class="form-control" value="Hotel Makkah: 100/100M" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Hotel Madinah *</label>
                                <input type="text" name="hotel_madinah" class="form-control" value="Hotel Madinah: 200/300M" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Food *</label>
                                <input type="text" name="food_service" class="form-control" value="Food: Breakfast, Lunch & Dinner" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Special Services *</label>
                                <input type="text" name="special_services" class="form-control" value="Special Services: Ziyarah Tour, Transportation & Guide" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Package Description / Tagline (EN)</label>
                                <textarea name="tagline" rows="2" class="form-control sb-rich-editor sb-rte-compact"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold">Create Pricing Card</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
