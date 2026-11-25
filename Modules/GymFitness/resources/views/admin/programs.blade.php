@extends('admin.layouts.master')

@section('title', 'Training Programs — FitPulse Gym Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Our Training Programs</h3>
                <span class="sb-badge-pill sb-badge-green">gym_programs</span>
            </div>
            <p class="text-muted small mb-0">Manage homepage training program cards (Weight Training, Cardio Fitness, CrossFit, Yoga &amp; Flexibility, Personal Training) in EN &amp; BN.</p>
        </div>

        <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addProgramModal">
            <i class="bi bi-plus-lg me-1"></i> Add Training Program
        </button>
    </div>

    <div class="sb-table-card">
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Program (EN / BN)</th>
                        <th>Subtitle</th>
                        <th>Duration &amp; Calories</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programs as $p)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $p->image }}" alt="{{ $p->title }}" style="width:68px;height:48px;object-fit:cover;border-radius:10px;border:1px solid #E2E8F0;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $p->title }}</div>
                                        <div class="text-muted small">{{ $p->title_bn }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $p->subtitle }}</div>
                                <div class="text-muted small">{{ $p->subtitle_bn }}</div>
                            </td>
                            <td>
                                <span class="sb-badge-pill sb-badge-blue">{{ $p->duration_label }}</span>
                                <span class="sb-badge-pill sb-badge-amber ms-1">{{ $p->calories_burn }}</span>
                            </td>
                            <td>
                                <span class="sb-badge-pill {{ $p->is_active ? 'sb-badge-green' : 'sb-badge-amber' }}">
                                    {{ $p->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editProg{{ $p->id }}"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('admin.gymfitness.programs.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Delete program {{ $p->title }}?')">
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
    </div>

    @foreach($programs as $p)
        <div class="modal fade" id="editProg{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                    <form action="{{ route('admin.gymfitness.programs.update', $p->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-bottom px-4 py-3">
                            <h5 class="modal-title fw-bold">Edit Program: {{ $p->title }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Title (EN) *</label>
                                    <input type="text" name="title" value="{{ $p->title }}" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Title (BN)</label>
                                    <input type="text" name="title_bn" value="{{ $p->title_bn }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Subtitle (EN)</label>
                                    <input type="text" name="subtitle" value="{{ $p->subtitle }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Subtitle (BN)</label>
                                    <input type="text" name="subtitle_bn" value="{{ $p->subtitle_bn }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Duration Label</label>
                                    <input type="text" name="duration_label" value="{{ $p->duration_label }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Calories Burn</label>
                                    <input type="text" name="calories_burn" value="{{ $p->calories_burn }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Image Upload</label>
                                    <input type="file" name="image_file" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="prAct{{ $p->id }}" {{ $p->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold" for="prAct{{ $p->id }}">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-bold px-4">Update Program</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Add Program Modal --}}
    <div class="modal fade" id="addProgramModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                <form action="{{ route('admin.gymfitness.programs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Add Training Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Title (EN) *</label>
                                <input type="text" name="title" class="form-control" required placeholder="e.g. HIIT & Functional">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Title (BN)</label>
                                <input type="text" name="title_bn" class="form-control" placeholder="ফাংশনাল ট্রেনিং">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Subtitle (EN)</label>
                                <input type="text" name="subtitle" class="form-control" placeholder="Burn fat & boost agility">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Subtitle (BN)</label>
                                <input type="text" name="subtitle_bn" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Duration Label</label>
                                <input type="text" name="duration_label" value="60 Mins" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Calories Burn</label>
                                <input type="text" name="calories_burn" value="650 kcal" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Program Image</label>
                                <input type="file" name="image_file" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="newPrAct" checked>
                                    <label class="form-check-label small fw-bold" for="newPrAct">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold px-4">Create Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
