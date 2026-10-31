@extends('admin.layouts.master')

@section('title', 'Skills & Technologies CRUD — Developer Portfolio')

@section('admin_content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 style="font-size: 23px; font-weight: 800; color: #0F172A; margin: 0;">Skills &amp; Technologies</h3>
            <p class="text-muted small mb-0">Manage technology cards, icons, proficiency levels, and categories stored in <code>port_skills</code>.</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.portfolio.skills.index') }}" method="GET" class="d-flex gap-2">
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 150px;">
                    <option value="">All Categories</option>
                    @foreach($categories as $c)
                        <option value="{{ $c }}" {{ $category === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search skills..." class="form-control form-control-sm">
            </form>
            <button type="button" class="btn btn-primary btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                <i class="bi bi-plus-lg me-1"></i> Add Skill
            </button>
        </div>
    </div>

    <div class="sb-table-card">
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Skill</th>
                        <th>Category</th>
                        <th>Proficiency</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($skills as $sk)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-dark" style="width: 38px; height: 38px; color: {{ $sk->icon_color }}; font-size: 18px;">
                                        <i class="{{ $sk->icon_class ?: 'fa-solid fa-code' }}"></i>
                                    </span>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $sk->name }}</div>
                                        <small class="text-muted">{{ $sk->subtitle }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="sb-badge-pill sb-badge-blue">{{ $sk->category }}</span></td>
                            <td><strong>{{ $sk->level_percent }}%</strong></td>
                            <td>#{{ $sk->sort_order }}</td>
                            <td><span class="sb-badge-pill {{ $sk->is_active ? 'sb-badge-green' : 'sb-badge-amber' }}">{{ $sk->is_active ? 'Active' : 'Hidden' }}</span></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editSkill{{ $sk->id }}"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('admin.portfolio.skills.destroy', $sk->id) }}" method="POST" onsubmit="return confirm('Delete skill {{ $sk->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="editSkill{{ $sk->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="{{ route('admin.portfolio.skills.update', $sk->id) }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Skill: {{ $sk->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Skill Name *</label>
                                                <input type="text" name="name" value="{{ $sk->name }}" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Category *</label>
                                                <select name="category" class="form-select">
                                                    @foreach($categories as $c)
                                                        <option value="{{ $c }}" {{ $sk->category === $c ? 'selected' : '' }}>{{ $c }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Subtitle</label>
                                                <input type="text" name="subtitle" value="{{ $sk->subtitle }}" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">FontAwesome Icon Class</label>
                                                <input type="text" name="icon_class" value="{{ $sk->icon_class }}" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Icon Hex Color</label>
                                                <input type="text" name="icon_color" value="{{ $sk->icon_color }}" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Proficiency (%)</label>
                                                <input type="number" name="level_percent" value="{{ $sk->level_percent }}" min="1" max="100" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Sort Order</label>
                                                <input type="number" name="sort_order" value="{{ $sk->sort_order }}" class="form-control">
                                            </div>
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="skAct{{ $sk->id }}" {{ $sk->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label small fw-semibold" for="skAct{{ $sk->id }}">Active</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary fw-bold">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $skills->links() }}</div>
    </div>

    <div class="modal fade" id="addSkillModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.portfolio.skills.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Skill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Skill Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. TypeScript" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Category *</label>
                            <select name="category" class="form-select">
                                @foreach($categories as $c)
                                    <option value="{{ $c }}">{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Subtitle</label>
                            <input type="text" name="subtitle" class="form-control" placeholder="e.g. Type-Safe JS">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">FontAwesome Icon Class</label>
                            <input type="text" name="icon_class" class="form-control" value="fa-solid fa-code">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Icon Color</label>
                            <input type="text" name="icon_color" class="form-control" value="#078BFF">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Proficiency (%)</label>
                            <input type="number" name="level_percent" class="form-control" value="95" min="1" max="100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Add Skill</button>
                </div>
            </form>
        </div>
    </div>
@endsection
