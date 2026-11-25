@extends('admin.layouts.master')

@section('title', 'Explore by Topic - WonderLearn Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="color: #0B2545;">Explore by Topic Cards (wl_topics)</h3>
        <p class="text-muted small mb-0">Manage the 6 pastel topic cards shown in the "Explore by Topic" section.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addTopicModal">
        <i class="bi bi-plus-lg me-1"></i> Add Topic Card
    </button>
</div>

<div class="row g-3">
    @foreach($topics as $topic)
        <div class="col-md-4 col-lg-4">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-2 rounded-3 d-flex align-items-center justify-content-center"
                         style="width: 64px; height: 64px; background: {{ $topic->card_bg }}; border: 1.5px solid {{ $topic->border_color }};">
                        <img src="{{ $topic->image }}" alt="{{ $topic->title }}" style="max-width: 48px; max-height: 48px; object-fit: contain;">
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">{{ $topic->title }}</h5>
                        <small class="text-muted">{{ $topic->age_group }} • {{ $topic->activities_count }} Activities</small>
                    </div>
                </div>
                <p class="small text-secondary mb-3">{{ $topic->description }}</p>

                <form action="{{ route('admin.wonderlearn.topics.update', $topic->id) }}" method="POST" enctype="multipart/form-data" class="border-top pt-3">
                    @csrf
                    @method('PUT')
                    <div class="row g-2">
                        <div class="col-7">
                            <label class="form-label small fw-bold mb-1">Title</label>
                            <input type="text" name="title" value="{{ $topic->title }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-5">
                            <label class="form-label small fw-bold mb-1">Card Hex Color</label>
                            <input type="text" name="card_bg" value="{{ $topic->card_bg }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold mb-1">Description</label>
                            <input type="text" name="description" value="{{ $topic->description }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-8">
                            <label class="form-label small fw-bold mb-1">Replace Icon</label>
                            <input type="file" name="image_file" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-bold mb-1">Order</label>
                            <input type="number" name="sort_order" value="{{ $topic->sort_order }}" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="submit" class="btn btn-sm btn-primary rounded-3 fw-semibold">Save Changes</button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                onclick="if(confirm('Delete this topic?')) document.getElementById('del-topic-{{ $topic->id }}').submit();">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </form>
                <form id="del-topic-{{ $topic->id }}" action="{{ route('admin.wonderlearn.topics.destroy', $topic->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Topic Modal -->
<div class="modal fade" id="addTopicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.wonderlearn.topics.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New Explore Topic</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Topic Title *</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Coding Club">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Card Background Color</label>
                        <input type="text" name="card_bg" value="#FFF4CC" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Age Group</label>
                        <input type="text" name="age_group" value="Ages 4-12" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Description</label>
                    <textarea name="description" rows="2" class="form-control"></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Topic Icon Image</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded-3 fw-semibold">Create Topic</button>
            </div>
        </form>
    </div>
</div>
@endsection
