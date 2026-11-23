@extends('admin.layouts.master')

@section('title', 'Recent Memories & Gallery - Eventify Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="color: #071E24;">Recent Memories 2024 & Footer Gallery (em_memories)</h3>
        <p class="text-muted small mb-0">Manage featured memory photos and the 2x3 "Our Recent Event Gallery" in the footer.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addMemoryModal">
        <i class="bi bi-plus-lg me-1"></i> Upload Memory Photo
    </button>
</div>

<div class="row g-3">
    @foreach($memories as $mem)
        <div class="col-md-4 col-lg-3">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100">
                <img src="{{ $mem->image }}" alt="{{ $mem->title }}" class="w-100 rounded-3 mb-2" style="height: 135px; object-fit: cover;">
                <span class="badge {{ $mem->is_footer_gallery ? 'bg-secondary' : 'bg-success' }} rounded-pill mb-2">
                    {{ $mem->is_footer_gallery ? 'Footer 2x3 Gallery' : 'Main Memories 2024 Section' }}
                </span>

                <form action="{{ route('admin.eventmanagement.memories.update', $mem->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-2">
                        <input type="text" name="title" value="{{ $mem->title }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <input type="file" name="image_file" class="form-control form-control-sm" accept="image/*">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_footer_gallery" value="1" id="fg-{{ $mem->id }}" {{ $mem->is_footer_gallery ? 'checked' : '' }}>
                        <label class="form-check-label small" for="fg-{{ $mem->id }}">Show in Footer Mini Gallery</label>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-sm btn-primary rounded-3 fw-semibold">Update</button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                onclick="if(confirm('Delete photo?')) document.getElementById('del-mem-{{ $mem->id }}').submit();">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </form>
                <form id="del-mem-{{ $mem->id }}" action="{{ route('admin.eventmanagement.memories.destroy', $mem->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    @endforeach
</div>

<div class="modal fade" id="addMemoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.eventmanagement.memories.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Upload Event Memory Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Title *</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Recent Memories 2024">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Photo File *</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_footer_gallery" value="1" id="newFg">
                    <label class="form-check-label small fw-semibold" for="newFg">Display in Footer 2x3 Mini Gallery</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded-3 fw-semibold">Upload Photo</button>
            </div>
        </form>
    </div>
</div>
@endsection
