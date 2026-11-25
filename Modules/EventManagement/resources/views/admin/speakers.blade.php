@extends('admin.layouts.master')

@section('title', 'Event Speakers - Eventify Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="color: #071E24;">Our Event Speakers (em_speakers)</h3>
        <p class="text-muted small mb-0">Manage the circular speaker portraits shown in "Our Event Speakers" section.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addSpeakerModal">
        <i class="bi bi-plus-lg me-1"></i> Add Event Speaker
    </button>
</div>

<div class="row g-3">
    @foreach($speakers as $speaker)
        <div class="col-md-6 col-lg-3">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100">
                <div class="text-center mb-3">
                    <img src="{{ $speaker->avatar }}" alt="{{ $speaker->name }}"
                         style="width: 96px; height: 96px; object-fit: cover; border-radius: 50%; border: 3px solid #B8FE37;">
                    <h5 class="fw-bold mb-0 mt-2">{{ $speaker->name }}</h5>
                    <small class="text-muted">{{ $speaker->designation }}</small>
                </div>

                <form action="{{ route('admin.eventmanagement.speakers.update', $speaker->id) }}" method="POST" enctype="multipart/form-data" class="border-top pt-3">
                    @csrf
                    @method('PUT')
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Speaker Name</label>
                        <input type="text" name="name" value="{{ $speaker->name }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Designation / Role</label>
                        <input type="text" name="designation" value="{{ $speaker->designation }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-8">
                            <label class="form-label small fw-bold mb-1">Photo</label>
                            <input type="file" name="avatar_file" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-bold mb-1">Order</label>
                            <input type="number" name="sort_order" value="{{ $speaker->sort_order }}" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="submit" class="btn btn-sm btn-primary rounded-3 fw-semibold">Save</button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                onclick="if(confirm('Remove this speaker?')) document.getElementById('del-sp-{{ $speaker->id }}').submit();">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </form>
                <form id="del-sp-{{ $speaker->id }}" action="{{ route('admin.eventmanagement.speakers.destroy', $speaker->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Speaker Modal -->
<div class="modal fade" id="addSpeakerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.eventmanagement.speakers.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Event Speaker</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Full Name *</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Alex Robertson">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Designation *</label>
                    <input type="text" name="designation" class="form-control" required value="Finance Consultant">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Speaker Portrait Image</label>
                    <input type="file" name="avatar_file" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded-3 fw-semibold">Create Speaker</button>
            </div>
        </form>
    </div>
</div>
@endsection
