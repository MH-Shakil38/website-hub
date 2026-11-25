@extends('admin.layouts.master')

@section('title', 'Featured Adventures - WonderLearn Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="color: #0B2545;">Featured Adventures (wl_adventures)</h3>
        <p class="text-muted small mb-0">Manage the panoramic Featured Adventures carousel banners.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addAdventureModal">
        <i class="bi bi-plus-lg me-1"></i> Add Adventure Banner
    </button>
</div>

<div class="row g-3">
    @foreach($adventures as $adv)
        <div class="col-12">
            <div class="bg-white border rounded-4 p-3 shadow-sm">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <img src="{{ $adv->banner_image }}" alt="{{ $adv->title }}" class="w-100 rounded-3 border" style="height: 110px; object-fit: cover;">
                    </div>
                    <div class="col-md-7">
                        <form action="{{ route('admin.wonderlearn.adventures.update', $adv->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row g-2">
                                <div class="col-md-7">
                                    <label class="form-label small fw-bold mb-1">Adventure Title</label>
                                    <input type="text" name="title" value="{{ $adv->title }}" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold mb-1">Button Text</label>
                                    <input type="text" name="button_text" value="{{ $adv->button_text }}" class="form-control form-control-sm">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold mb-1">Description</label>
                                    <input type="text" name="description" value="{{ $adv->description }}" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold mb-1">Replace Banner Art</label>
                                    <input type="file" name="image_file" class="form-control form-control-sm" accept="image/*">
                                </div>
                                <div class="col-md-4 d-flex align-items-end justify-content-end gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-3 fw-semibold">Update</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                            onclick="if(confirm('Delete this adventure?')) document.getElementById('del-adv-{{ $adv->id }}').submit();">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <form id="del-adv-{{ $adv->id }}" action="{{ route('admin.wonderlearn.adventures.destroy', $adv->id) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Adventure Modal -->
<div class="modal fade" id="addAdventureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.wonderlearn.adventures.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Featured Adventure</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Adventure Title *</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Island of Imagination">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Description *</label>
                    <textarea name="description" rows="2" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Button Text</label>
                    <input type="text" name="button_text" value="Start Adventure" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Panoramic Banner Image</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded-3 fw-semibold">Save Adventure</button>
            </div>
        </form>
    </div>
</div>
@endsection
