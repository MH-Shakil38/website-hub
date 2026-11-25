@extends('admin.layouts.master')

@section('title', 'Ticket Pricing Plans - Eventify Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="color: #071E24;">Get Your Entry Ticket — Pricing Plans (em_pricing_plans)</h3>
        <p class="text-muted small mb-0">Manage REGULAR ($49), PREMIUM ($69 Neon Lime Highlighted), and PLATINUM ($99) conference passes.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addPlanModal">
        <i class="bi bi-plus-lg me-1"></i> Add Ticket Plan
    </button>
</div>

<div class="row g-3">
    @foreach($plans as $plan)
        <div class="col-md-4">
            <div class="border rounded-4 p-4 shadow-sm h-100"
                 style="background: {{ $plan->is_highlighted ? '#ECFCCB' : '#FFFFFF' }};">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-dark rounded-pill px-3 py-1">{{ $plan->name }}</span>
                    @if($plan->is_highlighted)
                        <span class="badge bg-success rounded-pill">Neon Lime Featured</span>
                    @endif
                </div>
                <div class="fs-2 fw-bolder text-dark mb-3">{{ $plan->currency }}{{ number_format((float)$plan->price, 0) }}</div>

                <form action="{{ route('admin.eventmanagement.pricing.update', $plan->id) }}" method="POST" class="border-top pt-3">
                    @csrf
                    @method('PUT')
                    <div class="row g-2">
                        <div class="col-7">
                            <label class="form-label small fw-bold mb-1">Tier Name</label>
                            <input type="text" name="name" value="{{ $plan->name }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-5">
                            <label class="form-label small fw-bold mb-1">Price ($)</label>
                            <input type="number" step="0.01" name="price" value="{{ $plan->price }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-8">
                            <label class="form-label small fw-bold mb-1">Subtitle Label</label>
                            <input type="text" name="subtitle" value="{{ $plan->subtitle }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-bold mb-1">Order</label>
                            <input type="number" name="sort_order" value="{{ $plan->sort_order }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-12 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_highlighted" value="1" id="hl-{{ $plan->id }}" {{ $plan->is_highlighted ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold" for="hl-{{ $plan->id }}">
                                    Highlight Card in Neon Lime (#B8FE37)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="submit" class="btn btn-sm btn-primary rounded-3 fw-semibold">Save Plan</button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                onclick="if(confirm('Delete this ticket plan?')) document.getElementById('del-pl-{{ $plan->id }}').submit();">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </form>
                <form id="del-pl-{{ $plan->id }}" action="{{ route('admin.eventmanagement.pricing.destroy', $plan->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Ticket Plan Modal -->
<div class="modal fade" id="addPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.eventmanagement.pricing.store') }}" method="POST" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Ticket Pricing Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-7">
                        <label class="form-label small fw-bold">Plan Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. VIP EXECUTIVE">
                    </div>
                    <div class="col-5">
                        <label class="form-label small fw-bold">Price ($) *</label>
                        <input type="number" step="0.01" name="price" value="149" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Subtitle</label>
                        <input type="text" name="subtitle" value="ATTENDEE" class="form-control">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_highlighted" value="1" id="newPlanHl">
                            <label class="form-check-label small fw-semibold" for="newPlanHl">
                                Highlight Card in Neon Lime
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded-3 fw-semibold">Create Plan</button>
            </div>
        </form>
    </div>
</div>
@endsection
