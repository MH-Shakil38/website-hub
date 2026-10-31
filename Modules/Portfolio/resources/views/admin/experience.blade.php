@extends('admin.layouts.master')

@section('title', 'Experience & Education CRUD — Developer Portfolio')

@section('admin_content')
    <div class="row g-4">
        <!-- Left Column: Career Timeline (port_experiences) -->
        <div class="col-lg-6">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h4 class="fw-bold mb-0">Career Timeline</h4>
                    <small class="text-muted">Stored in <code>port_experiences</code></small>
                </div>
                <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addExpModal">
                    <i class="bi bi-plus-lg"></i> Add Role
                </button>
            </div>

            <div class="sb-table-card">
                <table class="sb-data-table">
                    <thead>
                        <tr>
                            <th>Period &amp; Role</th>
                            <th>Company</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($experiences as $exp)
                            <tr>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary mb-1">{{ $exp->period }}</span>
                                    <div class="fw-bold text-dark">{{ $exp->job_title }}</div>
                                    <small class="text-muted">{{ $exp->description }}</small>
                                </td>
                                <td>{{ $exp->company_name }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editExp{{ $exp->id }}"><i class="bi bi-pencil"></i></button>
                                        <form action="{{ route('admin.portfolio.experience.destroy', $exp->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="editExp{{ $exp->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('admin.portfolio.experience.update', $exp->id) }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Experience</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Period *</label>
                                                <input type="text" name="period" value="{{ $exp->period }}" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Job Title *</label>
                                                <input type="text" name="job_title" value="{{ $exp->job_title }}" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Company Name</label>
                                                <input type="text" name="company_name" value="{{ $exp->company_name }}" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Short Description</label>
                                                <textarea name="description" rows="2" class="form-control">{{ $exp->description }}</textarea>
                                            </div>
                                            <input type="hidden" name="is_active" value="1">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary fw-bold">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: Education & Certifications (port_educations) -->
        <div class="col-lg-6">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h4 class="fw-bold mb-0">Education &amp; Certifications</h4>
                    <small class="text-muted">Stored in <code>port_educations</code></small>
                </div>
                <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addEduModal">
                    <i class="bi bi-plus-lg"></i> Add Credential
                </button>
            </div>

            <div class="sb-table-card">
                <table class="sb-data-table">
                    <thead>
                        <tr>
                            <th>Credential</th>
                            <th>Institution</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($educations as $edu)
                            <tr>
                                <td>
                                    <span class="sb-badge-pill {{ $edu->type === 'Education' ? 'sb-badge-blue' : 'sb-badge-green' }} mb-1">{{ $edu->type }}</span>
                                    <div class="fw-bold text-dark">{{ $edu->title }}</div>
                                    <small class="text-muted">{{ $edu->completion_date }}</small>
                                </td>
                                <td>{{ $edu->institution }}</td>
                                <td>
                                    <form action="{{ route('admin.portfolio.education.destroy', $edu->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Experience Modal -->
    <div class="modal fade" id="addExpModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.portfolio.experience.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Career Experience</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Period (e.g. 2024 - Present) *</label>
                        <input type="text" name="period" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Job Title *</label>
                        <input type="text" name="job_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Company Name</label>
                        <input type="text" name="company_name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea name="description" rows="2" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary fw-bold">Add Experience</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Education Modal -->
    <div class="modal fade" id="addEduModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.portfolio.education.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Education or Certification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Type *</label>
                        <select name="type" class="form-select">
                            <option value="Education">Education</option>
                            <option value="Certification">Certification</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Degree / Course Name *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Institution *</label>
                        <input type="text" name="institution" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Completion Year</label>
                        <input type="text" name="completion_date" class="form-control" value="2025">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea name="description" rows="2" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary fw-bold">Add Record</button>
                </div>
            </form>
        </div>
    </div>
@endsection
