@extends('admin.layouts.master')

@section('title', 'Gym Management Dashboard — FitPulse Admin')

@section('admin_content')
    {{-- Top Header Banner --}}
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success-subtle text-success fw-bold px-3 py-1 rounded-pill">
                    <i class="bi bi-lightning-charge-fill me-1"></i> FitPulse Gym &amp; Fitness
                </span>
                <span class="sb-badge-pill sb-badge-green">gym_* tables</span>
            </div>
            <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Gym &amp; Fitness Management Dashboard</h3>
            <p class="text-muted small mb-0">Real-time overview of memberships, registrations, revenue, attendance, and class schedules.</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('gymfitness.home') }}" target="_blank" class="btn btn-outline-dark btn-sm fw-bold px-3 py-2 rounded-3">
                <i class="bi bi-box-arrow-up-right me-1"></i> View Public Website
            </a>
            <a href="{{ route('gymfitness.portal') }}" target="_blank" class="btn btn-outline-primary btn-sm fw-bold px-3 py-2 rounded-3">
                <i class="bi bi-person-badge me-1"></i> Member Portal
            </a>
            <a href="{{ route('admin.gymfitness.attendance.index') }}" class="btn btn-dark btn-sm fw-bold px-3 py-2 rounded-3">
                <i class="bi bi-qr-code-scan me-1"></i> QR Check-In
            </a>
            <a href="{{ route('admin.gymfitness.settings') }}" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3">
                <i class="bi bi-sliders me-1"></i> Website Builder
            </a>
        </div>
    </div>

    {{-- 8 KPI Cards Required by Prompt --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Total Members</div>
                    <div class="fs-3 fw-bold text-dark">{{ number_format($totalMembers) }}</div>
                    <span class="badge bg-primary-subtle text-primary small">Registered in gym_members</span>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#EFF6FF;color:#2563EB;font-size:22px;">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Active Members</div>
                    <div class="fs-3 fw-bold text-success">{{ number_format($activeMembers) }}</div>
                    <span class="badge bg-success-subtle text-success small">Valid Pass Holders</span>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#DCFCE7;color:#16A34A;font-size:22px;">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Expired Members</div>
                    <div class="fs-3 fw-bold text-danger">{{ number_format($expiredMembers) }}</div>
                    <span class="badge bg-danger-subtle text-danger small">Renewal Required</span>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#FEE2E2;color:#DC2626;font-size:22px;">
                    <i class="bi bi-person-exclamation"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Today's Registrations</div>
                    <div class="fs-3 fw-bold text-dark">{{ number_format($todayRegistrations) }}</div>
                    <span class="badge bg-info-subtle text-info small">{{ now()->format('M d, Y') }}</span>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#E0F2FE;color:#0284C7;font-size:22px;">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Monthly Revenue</div>
                    <div class="fs-3 fw-bold text-success">${{ number_format($monthlyRevenue, 2) }}</div>
                    <span class="text-muted small">Total Collected: ${{ number_format($totalRevenue, 2) }}</span>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#ECFCCB;color:#65A30D;font-size:22px;">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Pending / Due Payments</div>
                    <div class="fs-3 fw-bold text-warning">${{ number_format($pendingPayments, 2) }}</div>
                    <span class="badge bg-warning-subtle text-warning small">Awaiting Settlement</span>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#FEF3C7;color:#D97706;font-size:22px;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Today's Attendance</div>
                    <div class="fs-3 fw-bold text-primary">{{ number_format($todayAttendance) }}</div>
                    <span class="badge bg-primary-subtle text-primary small">QR &amp; ID Check-Ins</span>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#F3E8FF;color:#9333EA;font-size:22px;">
                    <i class="bi bi-qr-code-scan"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Upcoming Classes &amp; Coaches</div>
                    <div class="fs-3 fw-bold text-dark">{{ $upcomingClasses->count() }} <span class="fs-6 text-muted fw-normal">/ {{ $trainersCount }} Trainers</span></div>
                    <span class="badge bg-secondary-subtle text-secondary small">Weekly Schedule Active</span>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#F1F5F9;color:#334155;font-size:22px;">
                    <i class="bi bi-calendar2-week-fill"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2: Package Distribution & Upcoming Classes --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0">Membership Package Breakdown</h5>
                    <a href="{{ route('admin.gymfitness.packages.index') }}" class="btn btn-sm btn-outline-secondary">Manage Plans</a>
                </div>

                @foreach($packages as $pkg)
                    @php
                        $pct = $totalMembers > 0 ? round(($pkg->members_count / $totalMembers) * 100) : 0;
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small fw-bold mb-1">
                            <span>
                                {{ $pkg->name }} ({{ $pkg->currency }}{{ (float) $pkg->monthly_price }}/mo)
                                @if($pkg->is_featured) <span class="badge bg-success ms-1">Popular</span> @endif
                            </span>
                            <span>{{ $pkg->members_count }} Members ({{ $pct }}%)</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ max($pct, 8) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-lg-7">
            <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0">Upcoming Group Classes</h5>
                    <a href="{{ route('admin.gymfitness.classes.index') }}" class="btn btn-sm btn-outline-secondary">Full Schedule</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Class</th>
                                <th>Day &amp; Time</th>
                                <th>Trainer</th>
                                <th>Capacity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingClasses as $cls)
                                <tr>
                                    <td class="fw-bold">{{ $cls->title }}</td>
                                    <td class="small text-muted">{{ $cls->day_of_week }} • {{ $cls->time_slot }}</td>
                                    <td class="small">{{ $cls->trainer?->name ?? 'Coach' }}</td>
                                    <td><span class="sb-badge-pill sb-badge-green">{{ $cls->booked_count }}/{{ $cls->max_participants }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Recent Registrations & Recent Payments --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="sb-table-card">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Recent Member Registrations</h6>
                    <a href="{{ route('admin.gymfitness.members.index') }}" class="small fw-bold text-decoration-none">View All Members &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="sb-data-table">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Package</th>
                                <th>Expiry</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentMembers as $m)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $m->name }}</div>
                                        <div class="text-muted small">{{ $m->member_code }} • {{ $m->phone }}</div>
                                    </td>
                                    <td><span class="sb-badge-pill sb-badge-blue">{{ $m->package?->name ?? 'Plan' }}</span></td>
                                    <td class="small">{{ $m->expiry_date?->format('M d, Y') }}</td>
                                    <td>
                                        <span class="sb-badge-pill {{ $m->membership_status === 'Active' ? 'sb-badge-green' : 'sb-badge-amber' }}">
                                            {{ $m->membership_status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="sb-table-card">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Recent Payments &amp; Invoices</h6>
                    <a href="{{ route('admin.gymfitness.payments.index') }}" class="small fw-bold text-decoration-none">All Payments &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="sb-data-table">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Member</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPayments as $pay)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $pay->invoice_no }}</div>
                                        <div class="text-muted small">{{ $pay->payment_method }}</div>
                                    </td>
                                    <td class="small">{{ $pay->member?->name ?? 'Member' }}</td>
                                    <td class="fw-bold text-success">${{ number_format((float) $pay->amount, 2) }}</td>
                                    <td>
                                        <span class="sb-badge-pill {{ $pay->payment_status === 'Paid' ? 'sb-badge-green' : 'sb-badge-amber' }}">
                                            {{ $pay->payment_status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
