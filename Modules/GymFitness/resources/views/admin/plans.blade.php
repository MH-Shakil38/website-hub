@extends('admin.layouts.master')

@section('title', 'Workout Plans, Diet Plans & Member Progress — FitPulse Gym Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Workout Plans, Diet Plans &amp; Progress Tracking</h3>
                <span class="sb-badge-pill sb-badge-green">gym_fitness_plans + gym_progress_logs</span>
            </div>
            <p class="text-muted small mb-0">Create workout &amp; nutrition routines (EN / BN) and track member body measurements, weight, BMI, and body fat percentage.</p>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addProgressModal">
                <i class="bi bi-speedometer2 me-1"></i> Log Member Measurement
            </button>
            <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                <i class="bi bi-plus-lg me-1"></i> Create Workout / Diet Plan
            </button>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Workout & Diet Plans --}}
        <div class="col-lg-6">
            <div class="sb-table-card">
                <div class="p-3 border-bottom">
                    <h6 class="fw-bold mb-0">Workout &amp; Diet Plans (gym_fitness_plans)</h6>
                </div>
                <div class="table-responsive">
                    <table class="sb-data-table">
                        <thead>
                            <tr>
                                <th>Plan Title (EN / BN)</th>
                                <th>Type &amp; Goal</th>
                                <th>Schedule / Details</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $plan->title }}</div>
                                        <div class="text-muted small">{{ $plan->title_bn }}</div>
                                    </td>
                                    <td>
                                        <span class="sb-badge-pill {{ $plan->plan_type === 'Workout' ? 'sb-badge-blue' : 'sb-badge-green' }}">
                                            {{ $plan->plan_type }}
                                        </span>
                                        <div class="small text-muted mt-1">{{ $plan->goal }} ({{ $plan->duration_weeks }}w)</div>
                                    </td>
                                    <td class="small">{{ Str::limit($plan->schedule_details, 70) }}</td>
                                    <td>
                                        <form action="{{ route('admin.gymfitness.plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Delete plan?')">
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

        {{-- Right Column: Body Measurements & Weight Progress Logs --}}
        <div class="col-lg-6">
            <div class="sb-table-card">
                <div class="p-3 border-bottom">
                    <h6 class="fw-bold mb-0">Member Body Measurements &amp; Weight Logs (gym_progress_logs)</h6>
                </div>
                <div class="table-responsive">
                    <table class="sb-data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Member</th>
                                <th>Weight &amp; BMI</th>
                                <th>Measurements</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($progressLogs as $log)
                                <tr>
                                    <td class="small fw-semibold">{{ $log->recorded_date?->format('M d, Y') }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $log->member?->name ?? 'Member' }}</div>
                                        <div class="text-muted small">{{ $log->member?->member_code }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">{{ $log->weight_kg }} kg</div>
                                        <div class="small text-muted">BMI: {{ $log->bmi }}</div>
                                    </td>
                                    <td class="small">
                                        Fat: {{ $log->body_fat_pct ?? '—' }}% | Waist: {{ $log->waist_cm ?? '—' }}cm | Chest: {{ $log->chest_cm ?? '—' }}cm
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.gymfitness.plans.progress.destroy', $log->id) }}" method="POST">
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
    </div>

    {{-- Create Workout / Diet Plan Modal --}}
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                <form action="{{ route('admin.gymfitness.plans.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Create Workout or Diet Plan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Plan Title (EN) *</label>
                                <input type="text" name="title" class="form-control" required placeholder="e.g. 8-Week Lean Muscle Split">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Plan Title (BN)</label>
                                <input type="text" name="title_bn" class="form-control" placeholder="৮ সপ্তাহের মাংসপেশি গঠন প্ল্যান">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Plan Type *</label>
                                <select name="plan_type" class="form-select" required>
                                    <option value="Workout">Workout Plan</option>
                                    <option value="Diet">Diet Plan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Goal *</label>
                                <input type="text" name="goal" value="Muscle Gain" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Level *</label>
                                <select name="level" class="form-select" required>
                                    <option value="Beginner">Beginner</option>
                                    <option value="Intermediate">Intermediate</option>
                                    <option value="Advanced">Advanced</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Duration (Weeks) *</label>
                                <input type="number" name="duration_weeks" value="8" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Schedule / Routine Details (EN) *</label>
                                <textarea name="schedule_details" rows="3" class="form-control" required placeholder="Day 1: Chest & Triceps | Day 2: Back & Biceps..."></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Schedule / Routine Details (BN)</label>
                                <textarea name="schedule_details_bn" rows="2" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold px-4">Save Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Log Member Progress Modal --}}
    <div class="modal fade" id="addProgressModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                <form action="{{ route('admin.gymfitness.plans.progress.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Log Member Body Measurement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Select Member *</label>
                                <select name="member_id" class="form-select" required>
                                    @foreach($members as $m)
                                        <option value="{{ $m->id }}">{{ $m->member_code }} — {{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Weight (kg) *</label>
                                <input type="number" step="0.1" name="weight_kg" value="75.0" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Height (cm)</label>
                                <input type="number" step="0.1" name="height_cm" value="175" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Body Fat (%)</label>
                                <input type="number" step="0.1" name="body_fat_pct" value="16.5" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Chest (cm)</label>
                                <input type="number" step="0.1" name="chest_cm" value="104" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Waist (cm)</label>
                                <input type="number" step="0.1" name="waist_cm" value="82" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Arms (cm)</label>
                                <input type="number" step="0.1" name="arms_cm" value="38" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">Save Log</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
