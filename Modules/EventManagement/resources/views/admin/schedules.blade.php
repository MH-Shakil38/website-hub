@extends('admin.layouts.master')

@section('title', 'Event Schedule Plan - Eventify Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="color: #071E24;">Our Events Schedule Plan (em_schedules)</h3>
        <p class="text-muted small mb-0">Manage Day One, Day Two & Day Three timeline cards (01, 02, 03), session speakers, and venue time slots.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
        <i class="bi bi-plus-lg me-1"></i> Add Schedule Session
    </button>
</div>

<div class="row g-3">
    @foreach($schedules as $sch)
        <div class="col-lg-6">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge rounded-pill px-3 py-2 fw-bolder" style="background: #B8FE37; color: #071E24; font-size: 13px;">
                        #{{ $sch->session_number }} • {{ $sch->day_label }}
                    </span>
                    <img src="{{ $sch->image }}" alt="{{ $sch->title }}" style="width: 72px; height: 48px; object-fit: cover; border-radius: 8px;">
                    <div>
                        <h6 class="fw-bold mb-0">{{ $sch->title }}</h6>
                        <small class="text-muted">{{ $sch->time_range }} | {{ $sch->venue_location }}</small>
                    </div>
                </div>

                <form action="{{ route('admin.eventmanagement.schedules.update', $sch->id) }}" method="POST" enctype="multipart/form-data" class="border-top pt-3">
                    @csrf
                    @method('PUT')
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold mb-1">Day Tab</label>
                            <select name="day_key" class="form-select form-select-sm">
                                <option value="day1" {{ $sch->day_key === 'day1' ? 'selected' : '' }}>Day One</option>
                                <option value="day2" {{ $sch->day_key === 'day2' ? 'selected' : '' }}>Day Two</option>
                                <option value="day3" {{ $sch->day_key === 'day3' ? 'selected' : '' }}>Day Three</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold mb-1">No.</label>
                            <input type="text" name="session_number" value="{{ $sch->session_number }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">Session Title</label>
                            <input type="text" name="title" value="{{ $sch->title }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">Time Range</label>
                            <input type="text" name="time_range" value="{{ $sch->time_range }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">Venue Location</label>
                            <input type="text" name="venue_location" value="{{ $sch->venue_location }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">Speaker 1 (Name / Role)</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="speaker1_name" value="{{ $sch->speaker1_name }}" class="form-control" required>
                                <input type="text" name="speaker1_role" value="{{ $sch->speaker1_role }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">Speaker 2 (Name / Role)</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="speaker2_name" value="{{ $sch->speaker2_name }}" class="form-control">
                                <input type="text" name="speaker2_role" value="{{ $sch->speaker2_role }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold mb-1">Replace Photo</label>
                            <input type="file" name="image_file" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold mb-1">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ $sch->sort_order }}" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="submit" class="btn btn-sm btn-primary rounded-3 fw-semibold">Update Session</button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                onclick="if(confirm('Delete this schedule session?')) document.getElementById('del-sch-{{ $sch->id }}').submit();">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </form>
                <form id="del-sch-{{ $sch->id }}" action="{{ route('admin.eventmanagement.schedules.destroy', $sch->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('admin.eventmanagement.schedules.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Event Schedule Session</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Day Tab *</label>
                        <select name="day_key" class="form-select">
                            <option value="day1">Day One</option>
                            <option value="day2">Day Two</option>
                            <option value="day3">Day Three</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Session Number *</label>
                        <input type="text" name="session_number" value="04" class="form-control" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Time Slot *</label>
                        <input type="text" name="time_range" value="10:00 AM - 12:00 PM" class="form-control" required>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label small fw-bold">Session Title *</label>
                        <input type="text" name="title" class="form-control" required placeholder="Business World Event Introduction">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Venue Location *</label>
                        <input type="text" name="venue_location" value="26/C Asana, New York" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Speaker 1 Name</label>
                        <input type="text" name="speaker1_name" value="Alex Robertson" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Speaker 1 Role</label>
                        <input type="text" name="speaker1_role" value="UI/UX Designer" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Speaker 2 Name</label>
                        <input type="text" name="speaker2_name" value="Alexys Archer" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Speaker 2 Role</label>
                        <input type="text" name="speaker2_role" value="WP Developer" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Session Photo</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded-3 fw-semibold">Create Session</button>
            </div>
        </form>
    </div>
</div>
@endsection
