@extends('admin.layouts.master')

@section('title', 'Sponsors & Partners - Eventify Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="color: #071E24;">Sponsors & Partners (em_sponsors)</h3>
        <p class="text-muted small mb-0">Manage the company logos shown under "Join 4,000+ Companies Already Growing" (Nietzsche, Epicurious, Boltshift, Lightbox).</p>
    </div>
    <button type="button" class="btn btn-primary rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addSponsorModal">
        <i class="bi bi-plus-lg me-1"></i> Add Sponsor
    </button>
</div>

<div class="row g-3">
    @foreach($sponsors as $sp)
        <div class="col-md-3">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100">
                <div class="d-flex align-items-center gap-2 mb-3 p-3 rounded-3" style="background: #092229; color: #FFFFFF;">
                    <i class="bi {{ $sp->icon_class ?: 'bi-brightness-high-fill' }} fs-4" style="color: #B8FE37;"></i>
                    <span class="fw-bold fs-5">{{ $sp->name }}</span>
                </div>

                <form action="{{ route('admin.eventmanagement.sponsors.update', $sp->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Company Name</label>
                        <input type="text" name="name" value="{{ $sp->name }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-8">
                            <label class="form-label small fw-bold mb-1">Bootstrap Icon</label>
                            <input type="text" name="icon_class" value="{{ $sp->icon_class }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-bold mb-1">Order</label>
                            <input type="number" name="sort_order" value="{{ $sp->sort_order }}" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="submit" class="btn btn-sm btn-primary rounded-3 fw-semibold">Save</button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                onclick="if(confirm('Remove sponsor?')) document.getElementById('del-spon-{{ $sp->id }}').submit();">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </form>
                <form id="del-spon-{{ $sp->id }}" action="{{ route('admin.eventmanagement.sponsors.destroy', $sp->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    @endforeach
</div>

<div class="modal fade" id="addSponsorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.eventmanagement.sponsors.store') }}" method="POST" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Event Sponsor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Company Name *</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Nietzsche">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Bootstrap Icon Class</label>
                    <input type="text" name="icon_class" value="bi-lightning-charge-fill" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded-3 fw-semibold">Add Sponsor</button>
            </div>
        </form>
    </div>
</div>
@endsection
