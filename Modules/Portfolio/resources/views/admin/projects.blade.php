@extends('admin.layouts.master')

@section('title', 'Portfolio Projects & Gallery — Developer Portfolio Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Portfolio Projects &amp; Gallery</h3>
                <span class="sb-badge-pill sb-badge-blue">port_projects</span>
            </div>
            <p class="text-muted small mb-0">Manage featured projects, categories, multiple gallery images, and live URLs stored in <code>port_projects</code>.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('admin.portfolio.projects.index') }}" method="GET" class="d-flex gap-2">
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 170px;">
                    <option value="">All Categories</option>
                    @foreach($categories as $c)
                        <option value="{{ $c }}" {{ $category === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search projects..." style="width: 190px;">
            </form>
            <button type="button" class="btn btn-primary btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                <i class="bi bi-plus-lg me-1"></i> Add Project
            </button>
        </div>
    </div>

    <div class="sb-table-card">
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Technologies</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $proj)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $proj->thumbnail ?: '/module-media/Portfolio/theme/project-1.jpg' }}"
                                         alt="{{ $proj->title }}"
                                         style="width: 60px; height: 44px; object-fit: cover; border-radius: 8px; border: 1px solid #E2E8F0;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $proj->title }}</div>
                                        <div class="text-muted small">{{ $proj->subtitle }} • {{ $proj->completion_date }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="sb-badge-pill sb-badge-blue">{{ $proj->category }}</span></td>
                            <td>
                                <div class="d-flex flex-wrap gap-1" style="max-width: 240px;">
                                    @foreach(array_slice($proj->technologies ?? [], 0, 3) as $tech)
                                        <span class="badge bg-light text-dark border">{{ $tech }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                @if($proj->is_featured)
                                    <span class="sb-badge-pill sb-badge-purple">Featured</span>
                                @else
                                    <span class="text-muted small">Standard</span>
                                @endif
                            </td>
                            <td>
                                <span class="sb-badge-pill {{ $proj->is_active ? 'sb-badge-green' : 'sb-badge-amber' }}">
                                    {{ $proj->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('portfolio.project.show', $proj->slug) }}" target="_blank" class="sb-page-btn text-primary" title="View Details Page"><i class="bi bi-eye"></i></a>
                                    <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editProj{{ $proj->id }}" title="Edit Project"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('admin.portfolio.projects.destroy', $proj->id) }}" method="POST" onsubmit="return confirm('Delete project {{ $proj->title }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sb-page-btn text-danger" title="Delete Project"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $projects->links() }}</div>
    </div>

    <!-- Edit Project Modals (Placed outside table for valid HTML5 DOM parsing) -->
    @foreach($projects as $proj)
        <div class="modal fade" id="editProj{{ $proj->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content bg-white border-0 shadow" style="border-radius: 16px;">
                    <form action="{{ route('admin.portfolio.projects.update', $proj->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-bottom px-4 py-3">
                            <h5 class="modal-title fw-bold">Edit Project: {{ $proj->title }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Project Title *</label>
                                    <input type="text" name="title" value="{{ $proj->title }}" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Category *</label>
                                    <select name="category" class="form-select">
                                        @foreach($categories as $c)
                                            <option value="{{ $c }}" {{ $proj->category === $c ? 'selected' : '' }}>{{ $c }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Subtitle</label>
                                    <input type="text" name="subtitle" value="{{ $proj->subtitle }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Client Name</label>
                                    <input type="text" name="client_name" value="{{ $proj->client_name }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Completion Date</label>
                                    <input type="text" name="completion_date" value="{{ $proj->completion_date }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Live Website URL</label>
                                    <input type="text" name="live_url" value="{{ $proj->live_url }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">GitHub URL</label>
                                    <input type="text" name="github_url" value="{{ $proj->github_url }}" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Technologies (Comma separated)</label>
                                    <input type="text" name="technologies_raw" value="{{ implode(', ', $proj->technologies ?? []) }}" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Short Description</label>
                                    <input type="text" name="short_description" value="{{ $proj->short_description }}" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Full Project Case Study Description</label>
                                    <textarea name="description" rows="3" class="form-control">{{ $proj->description }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Replace Thumbnail Image</label>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <img src="{{ $proj->thumbnail ?: '/module-media/Portfolio/theme/project-1.jpg' }}" alt="Thumb" style="width: 56px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #CBD5E1;">
                                        <span class="text-muted small">Current Thumbnail</span>
                                    </div>
                                    <input type="file" name="thumbnail_file" accept="image/*" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Upload Gallery Images (Multiple)</label>
                                    <div class="text-muted small mb-2" style="min-height: 40px; display: flex; align-items: center;">
                                        {{ count($proj->gallery_images ?? []) }} gallery image(s) attached
                                    </div>
                                    <input type="file" name="gallery_files[]" accept="image/*" multiple class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="feat{{ $proj->id }}" {{ $proj->is_featured ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold" for="feat{{ $proj->id }}">Featured Project</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="act{{ $proj->id }}" {{ $proj->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold" for="act{{ $proj->id }}">Active / Published</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-bold">Update Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Project Modal -->
    <div class="modal fade" id="addProjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius: 16px;">
                <form action="{{ route('admin.portfolio.projects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Add New Portfolio Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Project Title *</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. FinTech SaaS Platform" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Category *</label>
                                <select name="category" class="form-select">
                                    @foreach($categories as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Subtitle</label>
                                <input type="text" name="subtitle" class="form-control" placeholder="e.g. Custom Web App">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Client Name</label>
                                <input type="text" name="client_name" class="form-control" placeholder="e.g. FinEdge Corp">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Completion Date</label>
                                <input type="text" name="completion_date" class="form-control" value="November 2025">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Live Website URL</label>
                                <input type="text" name="live_url" class="form-control" placeholder="https://example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">GitHub Repository URL</label>
                                <input type="text" name="github_url" class="form-control" placeholder="https://github.com/...">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Technologies Used (Comma separated)</label>
                                <input type="text" name="technologies_raw" class="form-control" value="Laravel, Next.js, Tailwind CSS, MySQL">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Short Description</label>
                                <input type="text" name="short_description" class="form-control" placeholder="Brief summary of the project...">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Full Project Description</label>
                                <textarea name="description" rows="3" class="form-control" placeholder="Detailed project architecture, features, and business impact..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Project Thumbnail Image</label>
                                <input type="file" name="thumbnail_file" accept="image/*" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Gallery Images (Multiple)</label>
                                <input type="file" name="gallery_files[]" accept="image/*" multiple class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-bold">Create Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
