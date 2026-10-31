@extends('admin.layouts.master')

@section('title', 'Blog & Articles CRUD — Developer Portfolio')

@section('admin_content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 style="font-size: 23px; font-weight: 800; color: #0F172A; margin: 0;">Blog Posts &amp; Articles</h3>
            <p class="text-muted small mb-0">Publish and edit developer blog posts stored in <code>port_blogs</code>.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#addBlogModal">
            <i class="bi bi-plus-lg me-1"></i> Publish Article
        </button>
    </div>

    <div class="sb-table-card">
        <table class="sb-data-table">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Category</th>
                    <th>Published Date</th>
                    <th>Views</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($blogs as $blog)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $blog->image ?: '/module-media/Portfolio/theme/blog-1.jpg' }}" alt="{{ $blog->title }}" style="width: 56px; height: 38px; border-radius: 7px; object-fit: cover;">
                                <div class="fw-bold text-dark">{{ $blog->title }}</div>
                            </div>
                        </td>
                        <td><span class="sb-badge-pill sb-badge-blue">{{ $blog->category }}</span></td>
                        <td>{{ $blog->published_date }}</td>
                        <td>{{ $blog->views }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('portfolio.blog.show', $blog->slug) }}" target="_blank" class="sb-page-btn text-primary"><i class="bi bi-eye"></i></a>
                                <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editBlog{{ $blog->id }}"><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('admin.portfolio.blogs.destroy', $blog->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="editBlog{{ $blog->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <form action="{{ route('admin.portfolio.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow" style="border-radius: 16px;">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit Article</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold small">Article Title *</label>
                                            <input type="text" name="title" value="{{ $blog->title }}" class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Category *</label>
                                            <input type="text" name="category" value="{{ $blog->category }}" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Published Date</label>
                                            <input type="text" name="published_date" value="{{ $blog->published_date }}" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Featured Image</label>
                                            <input type="file" name="image_file" accept="image/*" class="form-control">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold small">Short Summary</label>
                                            <input type="text" name="short_description" value="{{ $blog->short_description }}" class="form-control">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold small">Full Article Content (HTML supported)</label>
                                            <textarea name="content" rows="5" class="form-control">{{ $blog->content }}</textarea>
                                        </div>
                                        <input type="hidden" name="is_active" value="1">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary fw-bold">Update Article</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="addBlogModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form action="{{ route('admin.portfolio.blogs.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Publish New Blog Article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small">Article Title *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Category *</label>
                            <input type="text" name="category" class="form-control" value="Laravel" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Published Date</label>
                            <input type="text" name="published_date" class="form-control" value="{{ date('M d, Y') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Featured Image</label>
                            <input type="file" name="image_file" accept="image/*" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Short Summary</label>
                            <input type="text" name="short_description" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Full Article Content</label>
                            <textarea name="content" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary fw-bold">Publish Article</button>
                </div>
            </form>
        </div>
    </div>
@endsection
