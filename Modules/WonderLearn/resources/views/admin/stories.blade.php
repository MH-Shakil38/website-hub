@extends('admin.layouts.master')

@section('title', 'Magical Stories - WonderLearn Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="color: #0B2545;">Magical Stories (wl_stories)</h3>
        <p class="text-muted small mb-0">Manage illustrated story cards and read-aloud story content.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addStoryModal">
        <i class="bi bi-plus-lg me-1"></i> Add Magical Story
    </button>
</div>

<div class="row g-3">
    @foreach($stories as $story)
        <div class="col-md-6">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100">
                <div class="d-flex gap-3 mb-3">
                    <img src="{{ $story->image }}" alt="{{ $story->title }}" style="width: 120px; height: 80px; object-fit: cover; border-radius: 12px;">
                    <div>
                        <h5 class="fw-bold mb-1">{{ $story->title }}</h5>
                        <span class="badge rounded-pill" style="background: {{ $story->tag_bg }}; color: {{ $story->tag_color }};">
                            {{ $story->category_tag }}
                        </span>
                        <div class="small text-muted mt-1">{{ $story->reading_time }} • {{ $story->age_range }}</div>
                    </div>
                </div>

                <form action="{{ route('admin.wonderlearn.stories.update', $story->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-2">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold mb-1">Story Title</label>
                            <input type="text" name="title" value="{{ $story->title }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold mb-1">Category Badge</label>
                            <input type="text" name="category_tag" value="{{ $story->category_tag }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold mb-1">Full Read-Aloud Story Text</label>
                            <textarea name="story_content" rows="3" class="form-control form-control-sm">{{ $story->story_content }}</textarea>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold mb-1">Replace Illustration</label>
                            <input type="file" name="image_file" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold mb-1">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ $story->sort_order }}" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="submit" class="btn btn-sm btn-primary rounded-3 fw-semibold">Update Story</button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                onclick="if(confirm('Delete this story?')) document.getElementById('del-story-{{ $story->id }}').submit();">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </form>
                <form id="del-story-{{ $story->id }}" action="{{ route('admin.wonderlearn.stories.destroy', $story->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Story Modal -->
<div class="modal fade" id="addStoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.wonderlearn.stories.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Publish New Magical Story</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Story Title *</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. The Whispering Treehouse">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Category Tag *</label>
                        <input type="text" name="category_tag" value="Adventure" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Reading Time</label>
                        <input type="text" name="reading_time" value="5 min read" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Story Content</label>
                    <textarea name="story_content" rows="4" class="form-control" placeholder="Write the magical story here..."></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Story Cover Illustration</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded-3 fw-semibold">Publish Story</button>
            </div>
        </form>
    </div>
</div>
@endsection
