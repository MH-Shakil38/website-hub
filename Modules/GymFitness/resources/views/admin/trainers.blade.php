@extends('admin.layouts.master')

@section('title', 'Expert Trainers Management — FitPulse Gym Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Expert Trainers Management</h3>
                <span class="sb-badge-pill sb-badge-green">gym_trainers</span>
            </div>
            <p class="text-muted small mb-0">Manage gym trainers, specializations (EN / BN), schedules, assigned members, and salary/commission.</p>
        </div>

        <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addTrainerModal">
            <i class="bi bi-plus-lg me-1"></i> Add Trainer
        </button>
    </div>

    <div class="sb-table-card">
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Trainer (EN / BN)</th>
                        <th>Specialization</th>
                        <th>Experience &amp; Schedule</th>
                        <th>Assigned Members / Classes</th>
                        <th>Salary &amp; Commission</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trainers as $t)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $t->photo }}" alt="{{ $t->name }}" style="width:50px;height:50px;border-radius:12px;object-fit:cover;border:1px solid #E2E8F0;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $t->name }}</div>
                                        <div class="text-muted small">{{ $t->name_bn }} • {{ $t->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-primary">{{ $t->specialization }}</div>
                                <div class="text-muted small">{{ $t->specialization_bn }}</div>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $t->experience }}</div>
                                <div class="text-muted small">{{ $t->availability }}</div>
                            </td>
                            <td>
                                <span class="sb-badge-pill sb-badge-blue">{{ $t->members_count }} Members</span>
                                <span class="sb-badge-pill sb-badge-green ms-1">{{ $t->classes_count }} Classes</span>
                            </td>
                            <td>
                                <div class="small fw-bold">৳{{ number_format((float) $t->salary) }} /mo</div>
                                <div class="text-muted small">{{ (float) $t->commission_rate }}% PT Commission</div>
                            </td>
                            <td>
                                <span class="sb-badge-pill {{ $t->is_active ? 'sb-badge-green' : 'sb-badge-amber' }}">
                                    {{ $t->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editTrainer{{ $t->id }}" title="Edit"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('admin.gymfitness.trainers.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Delete trainer {{ $t->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sb-page-btn text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Edit Modals --}}
    @foreach($trainers as $t)
        <div class="modal fade" id="editTrainer{{ $t->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                    <form action="{{ route('admin.gymfitness.trainers.update', $t->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-bottom px-4 py-3">
                            <h5 class="modal-title fw-bold">Edit Trainer: {{ $t->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Name (EN) *</label>
                                    <input type="text" name="name" value="{{ $t->name }}" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Name (BN)</label>
                                    <input type="text" name="name_bn" value="{{ $t->name_bn }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Specialization (EN) *</label>
                                    <input type="text" name="specialization" value="{{ $t->specialization }}" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Specialization (BN)</label>
                                    <input type="text" name="specialization_bn" value="{{ $t->specialization_bn }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Experience (EN)</label>
                                    <input type="text" name="experience" value="{{ $t->experience }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Phone</label>
                                    <input type="text" name="phone" value="{{ $t->phone }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Email</label>
                                    <input type="email" name="email" value="{{ $t->email }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Availability / Shift</label>
                                    <input type="text" name="availability" value="{{ $t->availability }}" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Monthly Salary (BDT)</label>
                                    <input type="number" step="0.01" name="salary" value="{{ $t->salary }}" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Commission (%)</label>
                                    <input type="number" step="0.1" name="commission_rate" value="{{ $t->commission_rate }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Trainer Photo</label>
                                    <input type="file" name="photo_file" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="trAct{{ $t->id }}" {{ $t->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold" for="trAct{{ $t->id }}">Active Trainer</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-bold px-4">Update Trainer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Add Trainer Modal --}}
    <div class="modal fade" id="addTrainerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                <form action="{{ route('admin.gymfitness.trainers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Add New Gym Trainer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Name (EN) *</label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g. Rahim Rahman">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Name (BN)</label>
                                <input type="text" name="name_bn" class="form-control" placeholder="রহিম রহমান">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Specialization (EN) *</label>
                                <input type="text" name="specialization" class="form-control" required placeholder="Fitness Trainer">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Specialization (BN)</label>
                                <input type="text" name="specialization_bn" class="form-control" placeholder="ফিটনেস ট্রেইনার">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Experience</label>
                                <input type="text" name="experience" value="6+ Years Exp" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Phone</label>
                                <input type="text" name="phone" class="form-control" placeholder="+880 1711-000000">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Salary (BDT)</label>
                                <input type="number" name="salary" value="40000" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Trainer Photo</label>
                                <input type="file" name="photo_file" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="newTrAct" checked>
                                    <label class="form-check-label small fw-bold" for="newTrAct">Active on Public Website</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold px-4">Save Trainer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
