@extends('admin.layouts.master')

@section('title', 'Classes & Schedule Management — FitPulse Gym Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Weekly Class Schedule &amp; Bookings</h3>
                <span class="sb-badge-pill sb-badge-green">gym_classes</span>
            </div>
            <p class="text-muted small mb-0">Manage group classes (Yoga, Cardio, CrossFit, Strength), weekly time slots, assigned trainers, and member class bookings.</p>
        </div>

        <div class="d-flex gap-2">
            <form action="{{ route('admin.gymfitness.classes.index') }}" method="GET">
                <select name="day" class="form-select form-select-sm" onchange="this.form.submit()" style="width:150px;">
                    <option value="">All Days</option>
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $d)
                        <option value="{{ $d }}" {{ $dayFilter === $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </form>
            <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addClassModal">
                <i class="bi bi-plus-lg me-1"></i> Add Class Slot
            </button>
        </div>
    </div>

    {{-- Member Class Bookings Table --}}
    @if($bookings->count() > 0)
        <div class="sb-table-card mb-4">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-calendar2-check-fill text-success me-1"></i> Member Class Bookings (gym_class_bookings)</h6>
            </div>
            <div class="table-responsive">
                <table class="sb-data-table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Class Session</th>
                            <th>Day &amp; Time</th>
                            <th>Status</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $b)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $b->member?->name ?? 'Member' }}</div>
                                    <div class="text-muted small">{{ $b->member?->member_code }}</div>
                                </td>
                                <td class="fw-semibold">{{ $b->gymClass?->title ?? 'Class' }}</td>
                                <td class="small">{{ $b->gymClass?->day_of_week }} • {{ $b->gymClass?->time_slot }}</td>
                                <td>
                                    <span class="sb-badge-pill {{ $b->status === 'Booked' || $b->status === 'Attended' ? 'sb-badge-green' : 'sb-badge-amber' }}">
                                        {{ $b->status }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.gymfitness.classes.booking_status', $b->id) }}" method="POST" class="d-flex gap-1">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm" style="width:120px;">
                                            @foreach(['Booked', 'Attended', 'Cancelled'] as $st)
                                                <option value="{{ $st }}" {{ $b->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Weekly Classes Table --}}
    <div class="sb-table-card">
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Day &amp; Time Slot</th>
                        <th>Class Title (EN / BN)</th>
                        <th>Category</th>
                        <th>Assigned Trainer</th>
                        <th>Participants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $cls)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $cls->day_of_week }}</div>
                                <div class="text-primary small fw-semibold">{{ $cls->time_slot }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $cls->title }}</div>
                                <div class="text-muted small">{{ $cls->title_bn }}</div>
                            </td>
                            <td><span class="sb-badge-pill sb-badge-blue">{{ $cls->category }}</span></td>
                            <td class="small">{{ $cls->trainer?->name ?? 'Unassigned' }}</td>
                            <td><span class="sb-badge-pill sb-badge-green">{{ $cls->booked_count }} / {{ $cls->max_participants }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editClass{{ $cls->id }}"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('admin.gymfitness.classes.destroy', $cls->id) }}" method="POST" onsubmit="return confirm('Delete class slot?')">
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

    {{-- Edit Class Modals --}}
    @foreach($classes as $cls)
        <div class="modal fade" id="editClass{{ $cls->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                    <form action="{{ route('admin.gymfitness.classes.update', $cls->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-bottom px-4 py-3">
                            <h5 class="modal-title fw-bold">Edit Class Slot</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Title (EN) *</label>
                                    <input type="text" name="title" value="{{ $cls->title }}" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Title (BN)</label>
                                    <input type="text" name="title_bn" value="{{ $cls->title_bn }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Category</label>
                                    <input type="text" name="category" value="{{ $cls->category }}" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Day of Week</label>
                                    <select name="day_of_week" class="form-select">
                                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $d)
                                            <option value="{{ $d }}" {{ $cls->day_of_week === $d ? 'selected' : '' }}>{{ $d }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Time Slot</label>
                                    <input type="text" name="time_slot" value="{{ $cls->time_slot }}" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Assigned Trainer</label>
                                    <select name="trainer_id" class="form-select">
                                        <option value="">None</option>
                                        @foreach($trainers as $tr)
                                            <option value="{{ $tr->id }}" {{ $cls->trainer_id === $tr->id ? 'selected' : '' }}>{{ $tr->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Max Participants</label>
                                    <input type="number" name="max_participants" value="{{ $cls->max_participants }}" class="form-control">
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="clsAct{{ $cls->id }}" {{ $cls->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold" for="clsAct{{ $cls->id }}">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-bold px-4">Update Slot</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Add Class Modal --}}
    <div class="modal fade" id="addClassModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                <form action="{{ route('admin.gymfitness.classes.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Add Weekly Class Slot</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Class Title (EN) *</label>
                                <input type="text" name="title" class="form-control" required placeholder="e.g. CrossFit">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Class Title (BN)</label>
                                <input type="text" name="title_bn" class="form-control" placeholder="ক্রসফিট">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Category *</label>
                                <select name="category" class="form-select" required>
                                    <option value="CrossFit">CrossFit</option>
                                    <option value="Strength">Strength</option>
                                    <option value="Cardio">Cardio</option>
                                    <option value="Yoga">Yoga</option>
                                    <option value="Personal Training">Personal Training</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Day of Week *</label>
                                <select name="day_of_week" class="form-select" required>
                                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $d)
                                        <option value="{{ $d }}">{{ $d }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Time Slot *</label>
                                <input type="text" name="time_slot" value="6:00 AM - 7:00 AM" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Assigned Trainer</label>
                                <select name="trainer_id" class="form-select">
                                    <option value="">Select Trainer</option>
                                    @foreach($trainers as $tr)
                                        <option value="{{ $tr->id }}">{{ $tr->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Max Participants</label>
                                <input type="number" name="max_participants" value="25" class="form-control">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="newClsAct" checked>
                                    <label class="form-check-label small fw-bold" for="newClsAct">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold px-4">Create Class Slot</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
