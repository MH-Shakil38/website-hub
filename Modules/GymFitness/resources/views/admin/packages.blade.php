@extends('admin.layouts.master')

@section('title', 'Membership Packages — FitPulse Gym Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Membership Packages &amp; Pricing</h3>
                <span class="sb-badge-pill sb-badge-green">gym_packages</span>
            </div>
            <p class="text-muted small mb-0">Manage Basic, Premium, and Elite membership plans with monthly, quarterly, 6-month, yearly, and custom pricing in English &amp; Bengali.</p>
        </div>

        <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addPackageModal">
            <i class="bi bi-plus-lg me-1"></i> Create Membership Plan
        </button>
    </div>

    <div class="row g-4 mb-4">
        @foreach($packages as $pkg)
            <div class="col-lg-4">
                <div class="bg-white border {{ $pkg->is_featured ? 'border-success border-2' : '' }} rounded-4 p-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="sb-badge-pill {{ $pkg->is_active ? 'sb-badge-green' : 'sb-badge-amber' }}">
                                {{ $pkg->is_active ? 'Active' : 'Hidden' }}
                            </span>
                            @if($pkg->is_featured)
                                <span class="badge bg-success">{{ $pkg->badge_text ?: 'Most Popular' }}</span>
                            @endif
                        </div>

                        <h4 class="fw-bold mb-0">{{ $pkg->name }}</h4>
                        <div class="text-muted small mb-2">{{ $pkg->name_bn }} • {{ $pkg->subtitle }}</div>

                        <div class="fs-2 fw-bold text-success mb-2">
                            {{ $pkg->currency }}{{ (float) $pkg->monthly_price }} <span class="fs-6 text-muted fw-normal">/month</span>
                        </div>

                        <div class="row g-2 small bg-light rounded-3 p-2 mb-3">
                            <div class="col-6"><strong>Quarterly:</strong> {{ $pkg->currency }}{{ (float) $pkg->quarterly_price }}</div>
                            <div class="col-6"><strong>6-Month:</strong> {{ $pkg->currency }}{{ (float) $pkg->six_month_price }}</div>
                            <div class="col-6"><strong>Yearly:</strong> {{ $pkg->currency }}{{ (float) $pkg->yearly_price }}</div>
                            <div class="col-6"><strong>Reg. Fee:</strong> {{ $pkg->currency }}{{ (float) $pkg->registration_fee }}</div>
                        </div>

                        <ul class="list-unstyled small mb-3">
                            @foreach(($pkg->features ?? []) as $feat)
                                <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-1"></i> {{ $feat }}</li>
                            @endforeach
                        </ul>

                        <div class="d-flex flex-wrap gap-1 mb-3">
                            @if($pkg->has_personal_trainer) <span class="badge bg-primary-subtle text-primary">Personal Trainer</span> @endif
                            @if($pkg->has_group_classes) <span class="badge bg-info-subtle text-info">Group Classes</span> @endif
                            @if($pkg->has_locker) <span class="badge bg-secondary-subtle text-secondary">Locker</span> @endif
                            @if($pkg->has_nutrition) <span class="badge bg-warning-subtle text-warning">Nutrition Support</span> @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <span class="small text-muted fw-bold">{{ $pkg->members_count }} Enrolled Members</span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPkg{{ $pkg->id }}">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </button>
                            <form action="{{ route('admin.gymfitness.packages.destroy', $pkg->id) }}" method="POST" onsubmit="return confirm('Delete package {{ $pkg->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Edit Package Modal --}}
            <div class="modal fade" id="editPkg{{ $pkg->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                        <form action="{{ route('admin.gymfitness.packages.update', $pkg->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header border-bottom px-4 py-3">
                                <h5 class="modal-title fw-bold">Edit Package: {{ $pkg->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Package Name (EN) *</label>
                                        <input type="text" name="name" value="{{ $pkg->name }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Package Name (BN)</label>
                                        <input type="text" name="name_bn" value="{{ $pkg->name_bn }}" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Subtitle (EN)</label>
                                        <input type="text" name="subtitle" value="{{ $pkg->subtitle }}" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Subtitle (BN)</label>
                                        <input type="text" name="subtitle_bn" value="{{ $pkg->subtitle_bn }}" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Monthly Price *</label>
                                        <input type="number" step="0.01" name="monthly_price" value="{{ $pkg->monthly_price }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Quarterly Price *</label>
                                        <input type="number" step="0.01" name="quarterly_price" value="{{ $pkg->quarterly_price }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">6-Month Price *</label>
                                        <input type="number" step="0.01" name="six_month_price" value="{{ $pkg->six_month_price }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Yearly Price *</label>
                                        <input type="number" step="0.01" name="yearly_price" value="{{ $pkg->yearly_price }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Registration Fee</label>
                                        <input type="number" step="0.01" name="registration_fee" value="{{ $pkg->registration_fee }}" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Badge Text (EN)</label>
                                        <input type="text" name="badge_text" value="{{ $pkg->badge_text }}" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Button Text (EN)</label>
                                        <input type="text" name="button_text" value="{{ $pkg->button_text }}" class="form-control">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold">Features List (EN - Comma Separated)</label>
                                        <input type="text" name="features_raw" value="{{ implode(', ', $pkg->features ?? []) }}" class="form-control">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold">Features List (BN - Comma Separated)</label>
                                        <input type="text" name="features_bn_raw" value="{{ implode(', ', $pkg->features_bn ?? []) }}" class="form-control">
                                    </div>
                                    <div class="col-md-12 d-flex flex-wrap gap-4 pt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="has_personal_trainer" value="1" id="pt{{ $pkg->id }}" {{ $pkg->has_personal_trainer ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-semibold" for="pt{{ $pkg->id }}">Personal Trainer Access</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="has_group_classes" value="1" id="gc{{ $pkg->id }}" {{ $pkg->has_group_classes ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-semibold" for="gc{{ $pkg->id }}">Group Class Access</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="has_locker" value="1" id="lk{{ $pkg->id }}" {{ $pkg->has_locker ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-semibold" for="lk{{ $pkg->id }}">Locker Access</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="has_nutrition" value="1" id="nt{{ $pkg->id }}" {{ $pkg->has_nutrition ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-semibold" for="nt{{ $pkg->id }}">Nutrition Support</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="ft{{ $pkg->id }}" {{ $pkg->is_featured ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-semibold text-success" for="ft{{ $pkg->id }}">Featured (Most Popular)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="ac{{ $pkg->id }}" {{ $pkg->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-semibold" for="ac{{ $pkg->id }}">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-top px-4 py-3">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary fw-bold px-4">Update Package</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Add Package Modal --}}
    <div class="modal fade" id="addPackageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                <form action="{{ route('admin.gymfitness.packages.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Create Membership Package</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Package Name (EN) *</label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g. Student Plan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Package Name (BN)</label>
                                <input type="text" name="name_bn" class="form-control" placeholder="স্টুডেন্ট প্ল্যান">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Subtitle (EN)</label>
                                <input type="text" name="subtitle" class="form-control" placeholder="Special student rate">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Subtitle (BN)</label>
                                <input type="text" name="subtitle_bn" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Monthly Price *</label>
                                <input type="number" step="0.01" name="monthly_price" value="25" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Quarterly Price *</label>
                                <input type="number" step="0.01" name="quarterly_price" value="65" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">6-Month Price *</label>
                                <input type="number" step="0.01" name="six_month_price" value="120" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Yearly Price *</label>
                                <input type="number" step="0.01" name="yearly_price" value="210" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Features List (EN - Comma Separated)</label>
                                <input type="text" name="features_raw" value="Gym Access, Locker Access, Group Classes" class="form-control">
                            </div>
                            <div class="col-md-12 d-flex flex-wrap gap-4 pt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_personal_trainer" value="1" id="newPt">
                                    <label class="form-check-label small fw-semibold" for="newPt">Personal Trainer</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_group_classes" value="1" id="newGc" checked>
                                    <label class="form-check-label small fw-semibold" for="newGc">Group Classes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_locker" value="1" id="newLk" checked>
                                    <label class="form-check-label small fw-semibold" for="newLk">Locker Access</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_nutrition" value="1" id="newNt">
                                    <label class="form-check-label small fw-semibold" for="newNt">Nutrition Support</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="newAc" checked>
                                    <label class="form-check-label small fw-semibold" for="newAc">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold px-4">Create Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
