@extends('admin.layouts.master')

@section('title', 'Services Management CRUD — Developer Portfolio')

@section('admin_content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 style="font-size: 23px; font-weight: 800; color: #0F172A; margin: 0;">Developer Services</h3>
            <p class="text-muted small mb-0">Manage your service offerings and dedicated service details pages stored in <code>port_services</code>.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#addServiceModal">
            <i class="bi bi-plus-lg me-1"></i> Add Service
        </button>
    </div>

    <div class="sb-table-card">
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Subtitle</th>
                        <th>Features</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $srv)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary" style="width: 38px; height: 38px; font-size: 17px;">
                                        <i class="{{ $srv->icon_class ?: 'fa-solid fa-code' }}"></i>
                                    </span>
                                    <div class="fw-bold text-dark">{{ $srv->title }}</div>
                                </div>
                            </td>
                            <td>{{ $srv->subtitle }}</td>
                            <td><span class="badge bg-light text-dark border">{{ count($srv->features ?? []) }} Features</span></td>
                            <td><span class="sb-badge-pill {{ $srv->is_active ? 'sb-badge-green' : 'sb-badge-amber' }}">{{ $srv->is_active ? 'Active' : 'Hidden' }}</span></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('portfolio.service.show', $srv->slug) }}" target="_blank" class="sb-page-btn text-primary"><i class="bi bi-eye"></i></a>
                                    <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editSrv{{ $srv->id }}"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('admin.portfolio.services.destroy', $srv->id) }}" method="POST" onsubmit="return confirm('Delete service {{ $srv->title }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="editSrv{{ $srv->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="{{ route('admin.portfolio.services.update', $srv->id) }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Service: {{ $srv->title }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Service Title *</label>
                                                <input type="text" name="title" value="{{ $srv->title }}" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Card Subtitle</label>
                                                <input type="text" name="subtitle" value="{{ $srv->subtitle }}" class="form-control">
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label fw-semibold small">FontAwesome Icon</label>
                                                <input type="text" name="icon_class" value="{{ $srv->icon_class }}" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Sort Order</label>
                                                <input type="number" name="sort_order" value="{{ $srv->sort_order }}" class="form-control">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold small">Short Description</label>
                                                <input type="text" name="short_description" value="{{ $srv->short_description }}" class="form-control">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold small">Full Description</label>
                                                <textarea name="description" rows="3" class="form-control">{{ $srv->description }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold small">Key Features (Comma separated)</label>
                                                <input type="text" name="features_raw" value="{{ implode(', ', $srv->features ?? []) }}" class="form-control">
                                            </div>
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="srvAct{{ $srv->id }}" {{ $srv->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label small fw-semibold" for="srvAct{{ $srv->id }}">Active</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary fw-bold">Update Service</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.portfolio.services.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Service Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Cloud DevOps" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Card Subtitle</label>
                            <input type="text" name="subtitle" class="form-control" placeholder="e.g. AWS & Docker CI/CD">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">FontAwesome Icon</label>
                            <input type="text" name="icon_class" class="form-control" value="fa-solid fa-code">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Short Description</label>
                            <input type="text" name="short_description" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Full Description</label>
                            <textarea name="description" rows="3" class="form-control"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Included Features (Comma separated)</label>
                            <input type="text" name="features_raw" class="form-control" value="Clean Architecture, Fast Delivery, Ongoing Support">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Create Service</button>
                </div>
            </form>
        </div>
    </div>
@endsection
