@extends('admin.layouts.master')

@section('title', 'Eventify Dashboard - Event Management Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <span class="badge rounded-pill px-3 py-1 fw-bold mb-1" style="background: #ECFCCB; color: #3F6212;">
            <i class="bi bi-calendar2-event-fill me-1"></i> Eventify Conference Module (em_*)
        </span>
        <h3 class="fw-bolder mb-0" style="color: #071E24;">Eventify — Yearly Business Conferences “13” Hub</h3>
        <p class="text-muted small mb-0">Manage Event Speakers, Multi-Day Schedules, Ticket Pricing Tiers, Sponsors, Recent Memories & Attendee Bookings.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ url('/event-management') }}" target="_blank" class="btn btn-outline-dark rounded-3 fw-semibold">
            <i class="bi bi-box-arrow-up-right me-1"></i> Preview Eventify Frontend
        </a>
        <a href="{{ route('admin.eventmanagement.settings') }}" class="btn btn-dark rounded-3 fw-semibold" style="background: #071E24;">
            <i class="bi bi-sliders2 me-1" style="color: #B8FE37;"></i> Hero, Countdown & Venue
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Event Speakers</div>
                <div class="fs-3 fw-bolder text-dark">{{ $speakersCount }}</div>
                <a href="{{ route('admin.eventmanagement.speakers.index') }}" class="small text-primary text-decoration-none fw-semibold">Manage Speakers &rarr;</a>
            </div>
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #ECFCCB; color: #4D7C0F; font-size: 22px;">
                <i class="bi bi-mic-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Schedule Sessions</div>
                <div class="fs-3 fw-bolder text-dark">{{ $schedulesCount }}</div>
                <a href="{{ route('admin.eventmanagement.schedules.index') }}" class="small text-primary text-decoration-none fw-semibold">Manage Schedule &rarr;</a>
            </div>
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #DBEAFE; color: #2563EB; font-size: 22px;">
                <i class="bi bi-calendar2-week-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Ticket Bookings</div>
                <div class="fs-3 fw-bolder text-dark">{{ $bookingsCount }}</div>
                <a href="{{ route('admin.eventmanagement.bookings.index') }}" class="small text-primary text-decoration-none fw-semibold">View Attendees &rarr;</a>
            </div>
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #FEF3C7; color: #D97706; font-size: 22px;">
                <i class="bi bi-ticket-perforated-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Ticket Revenue</div>
                <div class="fs-3 fw-bolder text-success">${{ number_format((float)$totalRevenue, 0) }}</div>
                <a href="{{ route('admin.eventmanagement.pricing.index') }}" class="small text-primary text-decoration-none fw-semibold">{{ $pricingCount }} Pricing Tiers &rarr;</a>
            </div>
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #DCFCE7; color: #16A34A; font-size: 22px;">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Featured Speakers -->
    <div class="col-lg-5">
        <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Our Event Speakers</h5>
                <a href="{{ route('admin.eventmanagement.speakers.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Manage All</a>
            </div>
            <div class="d-flex flex-column gap-3">
                @foreach($recentSpeakers as $sp)
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-2">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $sp->avatar }}" alt="{{ $sp->name }}" style="width: 46px; height: 46px; object-fit: cover; border-radius: 50%; border: 2px solid #B8FE37;">
                            <div>
                                <div class="fw-bold text-dark">{{ $sp->name }}</div>
                                <small class="text-muted">{{ $sp->designation }}</small>
                            </div>
                        </div>
                        <span class="badge bg-success-subtle text-success rounded-pill">Active</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Ticket Bookings -->
    <div class="col-lg-7">
        <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Recent Conference Ticket Bookings</h5>
                <a href="{{ route('admin.eventmanagement.bookings.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">View All Bookings</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr class="text-muted small">
                            <th>Pass ID</th>
                            <th>Attendee</th>
                            <th>Plan</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $bk)
                            <tr>
                                <td><code class="fw-bold text-dark">{{ $bk->ticket_code }}</code></td>
                                <td>
                                    <div class="fw-bold">{{ $bk->attendee_name }}</div>
                                    <small class="text-muted">{{ $bk->email }}</small>
                                </td>
                                <td><span class="badge bg-dark text-white rounded-pill">{{ $bk->plan_name }} x{{ $bk->quantity }}</span></td>
                                <td class="fw-bold text-success">${{ number_format((float)$bk->total_amount, 2) }}</td>
                                <td><span class="badge bg-success-subtle text-success rounded-pill">{{ $bk->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
