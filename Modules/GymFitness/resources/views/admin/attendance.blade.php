@extends('admin.layouts.master')

@section('title', 'Attendance & QR Check-in — FitPulse Gym Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Attendance &amp; QR Check-In Desk</h3>
                <span class="sb-badge-pill sb-badge-green">gym_attendances</span>
            </div>
            <p class="text-muted small mb-0">Scan Member QR Code, enter Member ID (`FP-2026-XXXX`), or select a member to record daily check-in and check-out.</p>
        </div>

        <div class="d-flex gap-3">
            <div class="border rounded-3 px-3 py-2 bg-light text-center">
                <div class="text-muted small">Today's Check-Ins</div>
                <div class="fw-bold text-success fs-5">{{ $todayCount }}</div>
            </div>
            <div class="border rounded-3 px-3 py-2 bg-light text-center">
                <div class="text-muted small">Monthly Attendance</div>
                <div class="fw-bold text-primary fs-5">{{ $monthlyCount }}</div>
            </div>
        </div>
    </div>

    {{-- Instant QR Code / Member ID Check-In Bar --}}
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm">
        <div class="row g-3 align-items-end">
            <div class="col-lg-6">
                <form action="{{ route('admin.gymfitness.attendance.checkin') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input type="hidden" name="check_in_method" value="QR Code">
                    <div class="flex-grow-1">
                        <label class="form-label small fw-bold"><i class="bi bi-qr-code-scan text-success me-1"></i> Scan QR Token or Enter Member ID / Phone</label>
                        <input type="text" name="qr_or_code" class="form-control" placeholder="e.g. FP-2026-0001 or QR-FP-2026-0001" required autofocus>
                    </div>
                    <div class="d-flex align-items-end">
                        <button type="submit" class="btn btn-success fw-bold px-4" style="height:38px;">
                            <i class="bi bi-check2-circle me-1"></i> Instant Check-In
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-lg-6">
                <form action="{{ route('admin.gymfitness.attendance.checkin') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input type="hidden" name="check_in_method" value="Member ID">
                    <div class="flex-grow-1">
                        <label class="form-label small fw-bold">Or Select Member Manually</label>
                        <select name="member_id" class="form-select" required>
                            <option value="">Choose Member...</option>
                            @foreach($members as $m)
                                <option value="{{ $m->id }}">{{ $m->member_code }} — {{ $m->name }} ({{ $m->membership_status }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex align-items-end">
                        <button type="submit" class="btn btn-dark fw-bold px-4" style="height:38px;">Record Check-In</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Attendance History Table --}}
    <div class="sb-table-card">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold mb-0">Daily Attendance Log</h6>
            <form action="{{ route('admin.gymfitness.attendance.index') }}" method="GET" class="d-flex gap-2">
                <input type="date" name="date" value="{{ $dateFilter }}" class="form-control form-control-sm" onchange="this.form.submit()">
                @if($dateFilter)
                    <a href="{{ route('admin.gymfitness.attendance.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Member &amp; ID</th>
                        <th>Package</th>
                        <th>Check-In Time</th>
                        <th>Check-Out Time</th>
                        <th>Method</th>
                        <th>Status / Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $att)
                        <tr>
                            <td class="fw-semibold small">{{ $att->attendance_date?->format('M d, Y') }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $att->member?->name ?? 'Member' }}</div>
                                <div class="text-muted small">{{ $att->member?->member_code }}</div>
                            </td>
                            <td><span class="sb-badge-pill sb-badge-blue">{{ $att->member?->package?->name ?? 'Plan' }}</span></td>
                            <td class="fw-bold text-success small">{{ $att->check_in_time }}</td>
                            <td class="small">{{ $att->check_out_time ?: 'In Gym' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-qr-code me-1"></i> {{ $att->check_in_method }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if(!$att->check_out_time)
                                        <form action="{{ route('admin.gymfitness.attendance.checkout', $att->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary py-0 px-2">Check-Out</button>
                                        </form>
                                    @else
                                        <span class="sb-badge-pill sb-badge-green">Completed</span>
                                    @endif

                                    <form action="{{ route('admin.gymfitness.attendance.destroy', $att->id) }}" method="POST" onsubmit="return confirm('Delete attendance log?')">
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
        <div class="p-3">{{ $attendances->links() }}</div>
    </div>
@endsection
