@extends('admin.layouts.master')

@section('title', 'Blog & Travel Guides — Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Blog &amp; Hajj / Umrah Travel Guides</h3>
                <span class="sb-badge-pill sb-badge-green">hajj_blogs</span>
            </div>
            <p class="text-muted small mb-0">Publish multilingual travel checklists, Ihram guides, and Ziyarah tips in <code>hajj_blogs</code>.</p>
        </div>

        <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addBlogModal">
            <i class="bi bi-plus-lg me-1"></i> Publish Article
        </button>
    </div>

    <div class="sb-table-card">
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Article (EN / BN / AR)</th>
                        <th>Category</th>
                        <th>Published</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($blogs as $b)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $b->image }}" alt="{{ $b->title }}" style="width: 60px; height: 42px; border-radius: 8px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $b->title }}</div>
                                        <div class="text-muted small">{{ $b->title_bn }} • <span dir="rtl">{{ $b->title_ar }}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="sb-badge-pill sb-badge-green">{{ $b->category }}</span></td>
                            <td class="small">{{ $b->published_date }}</td>
                            <td>
                                <span class="sb-badge-pill {{ $b->is_active ? 'sb-badge-green' : 'sb-badge-amber' }}">
                                    {{ $b->is_active ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('hajjumrah.blog.show', $b->slug) }}" target="_blank" class="sb-page-btn text-primary"><i class="bi bi-eye"></i></a>
                                    <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editBlog{{ $b->id }}"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('admin.hajjumrah.blogs.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Delete article?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $blogs->links() }}</div>
    </div>

    <!-- Edit Modals -->
    @foreach($blogs as $b)
        <div class="modal fade" id="editBlog{{ $b->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content bg-white border-0 shadow" style="border-radius: 16px;">
                    <form action="{{ route('admin.hajjumrah.blogs.update', $b->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-bottom px-4 py-3">
                            <h5 class="modal-title fw-bold">Edit Article: {{ $b->title }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Title (EN) *</label>
                                    <input type="text" name="title" value="{{ $b->title }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Title (বাংলা)</label>
                                    <input type="text" name="title_bn" value="{{ $b->title_bn }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Title (العربية)</label>
                                    <input type="text" name="title_ar" value="{{ $b->title_ar }}" class="form-control" dir="rtl">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Category (EN) *</label>
                                    <input type="text" name="category" value="{{ $b->category }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Published Date</label>
                                    <input type="text" name="published_date" value="{{ $b->published_date }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Featured Image</label>
                                    <input type="file" name="image_file" accept="image/*" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Short Summary / Description (EN) *</label>
                                    <textarea name="short_description" rows="2" class="form-control sb-rich-editor sb-rte-compact" required>{{ $b->short_description }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Short Summary / Description (বাংলা)</label>
                                    <textarea name="short_description_bn" rows="2" class="form-control sb-rich-editor sb-rte-compact">{{ $b->short_description_bn }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Short Summary / Description (العربية)</label>
                                    <textarea name="short_description_ar" rows="2" class="form-control sb-rich-editor sb-rte-compact" dir="rtl">{{ $b->short_description_ar }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Full Article Content &amp; Details (EN)</label>
                                    <textarea name="content" rows="4" class="form-control sb-rich-editor">{{ $b->content }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Full Article Content &amp; Details (বাংলা)</label>
                                    <textarea name="content_bn" rows="4" class="form-control sb-rich-editor">{{ $b->content_bn }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Full Article Content &amp; Details (العربية)</label>
                                    <textarea name="content_ar" rows="4" class="form-control sb-rich-editor" dir="rtl">{{ $b->content_ar }}</textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $b->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold">Published</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success fw-bold">Update Article</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Modal -->
    <div class="modal fade" id="addBlogModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius: 16px;">
                <form action="{{ route('admin.hajjumrah.blogs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Publish New Hajj / Umrah Article</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Title (EN) *</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Title (বাংলা)</label>
                                <input type="text" name="title_bn" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Title (العربية)</label>
                                <input type="text" name="title_ar" class="form-control" dir="rtl">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Category *</label>
                                <input type="text" name="category" class="form-control" value="Travel Guide" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Image</label>
                                <input type="file" name="image_file" accept="image/*" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Short Summary / Description (EN) *</label>
                                <textarea name="short_description" rows="2" class="form-control sb-rich-editor sb-rte-compact" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Short Summary / Description (বাংলা)</label>
                                <textarea name="short_description_bn" rows="2" class="form-control sb-rich-editor sb-rte-compact"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Short Summary / Description (العربية)</label>
                                <textarea name="short_description_ar" rows="2" class="form-control sb-rich-editor sb-rte-compact" dir="rtl"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Full Article Content &amp; Details (EN)</label>
                                <textarea name="content" rows="4" class="form-control sb-rich-editor"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Full Article Content &amp; Details (বাংলা)</label>
                                <textarea name="content_bn" rows="4" class="form-control sb-rich-editor"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Full Article Content &amp; Details (العربية)</label>
                                <textarea name="content_ar" rows="4" class="form-control sb-rich-editor" dir="rtl"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold">Publish Article</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
