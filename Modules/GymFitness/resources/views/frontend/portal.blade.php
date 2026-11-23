@extends('gymfitness::frontend.layouts.master')

@section('gym_content')
@php
    $isBn = ($locale ?? 'en') === 'bn';
@endphp

<section class="gym-section" style="padding:46px 0 80px;">
    <div class="gym-container">
        {{-- Top Member Switcher & Portal Header --}}
        <div style="background:#121912;border:1px solid var(--gym-border-strong);border-radius:18px;padding:22px 24px;margin-bottom:28px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
            <div>
                <span class="gym-eyebrow"><i class="fa-solid fa-id-card-clip"></i> FITPULSE MEMBER PORTAL</span>
                <h1 style="font-size:26px;font-weight:900;color:#fff;margin:2px 0;">
                    {{ $member ? ($isBn ? 'স্বাগতম, ' . $member->name : 'Welcome back, ' . $member->name) : 'FitPulse Member Portal' }}
                </h1>
                <p style="font-size:13px;color:#9CA3AF;margin:0;">
                    Manage your membership, QR check-in pass, class bookings, workout/diet plans, and progress logs.
                </p>
            </div>

            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <form action="{{ route('gymfitness.portal.login') }}" method="POST" style="display:flex;gap:8px;">
                    @csrf
                    <input type="text" name="identifier" class="gym-input" style="height:38px;width:220px;font-size:12.5px;" placeholder="Member ID / Phone / Email" required>
                    <button type="submit" class="gym-btn-outline" style="padding:7px 14px;font-size:12.5px;">Switch Member</button>
                </form>

                @if($allMembers->count() > 1)
                    <form action="{{ route('gymfitness.portal') }}" method="GET">
                        <select name="member_id" class="gym-select" style="height:38px;font-size:12.5px;" onchange="this.form.submit()">
                            @foreach($allMembers as $m)
                                <option value="{{ $m->id }}" {{ $member && $member->id === $m->id ? 'selected' : '' }}>
                                    {{ $m->member_code }} — {{ $m->name }} ({{ $m->membership_status }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>
        </div>

        @if($member)
            {{-- Row 1: Member Profile + QR Check-in Pass + Membership Status Card --}}
            <div style="display:grid;grid-template-columns:1.1fr 0.9fr 1fr;gap:22px;margin-bottom:28px;">
                {{-- Card 1: Member Profile --}}
                <div style="background:#121912;border:1px solid rgba(255,255,255,0.08);border-radius:18px;padding:24px;">
                    <div style="display:flex;align-items:center;gap:16px;margin-bottom:18px;">
                        <img src="{{ $member->photo ?: '/module-media/GymFitness/theme/trainer-1.jpg' }}"
                             alt="{{ $member->name }}"
                             style="width:74px;height:74px;border-radius:50%;object-fit:cover;border:2px solid var(--gym-primary);">
                        <div>
                            <span style="display:inline-block;background:rgba(163,230,53,0.16);color:var(--gym-primary);font-size:11px;font-weight:800;padding:3px 10px;border-radius:999px;margin-bottom:4px;">
                                {{ $member->member_code }}
                            </span>
                            <h3 style="font-size:19px;font-weight:800;color:#fff;">{{ $member->name }}</h3>
                            <div style="font-size:12.5px;color:#9CA3AF;">{{ $member->email }} • {{ $member->phone }}</div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:12.5px;border-top:1px solid rgba(255,255,255,0.08);padding-top:14px;">
                        <div><span style="color:#9CA3AF;">Gender / Blood:</span> <strong style="color:#fff;">{{ $member->gender }} ({{ $member->blood_group ?: 'O+' }})</strong></div>
                        <div><span style="color:#9CA3AF;">Assigned Trainer:</span> <strong style="color:var(--gym-primary);">{{ $member->trainer?->name ?? 'General Floor Coach' }}</strong></div>
                        <div><span style="color:#9CA3AF;">Emergency Contact:</span> <strong style="color:#fff;">{{ $member->emergency_contact_name ?: 'N/A' }}</strong></div>
                        <div><span style="color:#9CA3AF;">Emergency Phone:</span> <strong style="color:#fff;">{{ $member->emergency_contact_phone ?: 'N/A' }}</strong></div>
                        <div style="grid-column:span 2;"><span style="color:#9CA3AF;">Address:</span> <strong style="color:#fff;">{{ $member->address ?: 'Dhaka, Bangladesh' }}</strong></div>
                    </div>
                </div>

                {{-- Card 2: Digital QR Code Check-in Pass --}}
                <div style="background:linear-gradient(155deg,#142614 0%,#0D140D 100%);border:1px solid var(--gym-primary);border-radius:18px;padding:24px;text-align:center;display:flex;flex-direction:column;justify-content:space-between;align-items:center;">
                    <div>
                        <span style="font-size:11px;font-weight:800;letter-spacing:0.12em;color:var(--gym-primary);text-transform:uppercase;">DIGITAL GYM ACCESS PASS</span>
                        <h4 style="font-size:16px;font-weight:800;color:#fff;margin:4px 0 12px;">QR Code Check-In</h4>
                    </div>

                    {{-- Dynamic SVG QR Code Pattern --}}
                    <div style="background:#FFFFFF;padding:12px;border-radius:14px;display:inline-block;margin-bottom:12px;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($member->qr_token ?: $member->member_code) }}"
                             alt="QR Pass {{ $member->member_code }}"
                             style="width:112px;height:112px;display:block;">
                    </div>

                    <div style="font-size:12px;color:#CBD5E1;margin-bottom:12px;">
                        Token: <strong style="color:var(--gym-primary);">{{ $member->qr_token ?: $member->member_code }}</strong>
                    </div>

                    <form action="{{ route('gymfitness.portal.checkin') }}" method="POST" style="width:100%;">
                        @csrf
                        <input type="hidden" name="member_id" value="{{ $member->id }}">
                        <button type="submit" class="gym-btn-lime" style="width:100%;padding:9px 16px;font-size:12.5px;">
                            <i class="fa-solid fa-qrcode"></i> Instant QR Check-In Now
                        </button>
                    </form>
                </div>

                {{-- Card 3: Membership Status & Quick Renewal / Upgrade --}}
                <div style="background:#121912;border:1px solid rgba(255,255,255,0.08);border-radius:18px;padding:24px;display:flex;flex-direction:column;justify-content:space-between;">
                    <div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                            <span style="font-size:12px;color:#9CA3AF;font-weight:700;">CURRENT PLAN</span>
                            <span style="padding:3px 11px;border-radius:999px;font-size:11.5px;font-weight:800;background:{{ $member->membership_status === 'Active' ? 'rgba(163,230,53,0.18)' : 'rgba(239,68,68,0.2)' }};color:{{ $member->membership_status === 'Active' ? 'var(--gym-primary)' : '#FCA5A5' }};">
                                {{ $member->membership_status }}
                            </span>
                        </div>

                        <h3 style="font-size:22px;font-weight:900;color:#fff;margin-bottom:4px;">
                            {{ $member->package?->name ?? 'Basic Plan' }}
                        </h3>
                        <p style="font-size:12.5px;color:#9CA3AF;margin-bottom:14px;">
                            Billing Cycle: <strong style="color:#fff;text-transform:capitalize;">{{ str_replace('_', ' ', $member->billing_cycle) }}</strong>
                            • Expires: <strong style="color:var(--gym-primary);">{{ $member->expiry_date ? $member->expiry_date->format('M d, Y') : 'N/A' }}</strong>
                        </p>
                    </div>

                    <form action="{{ route('gymfitness.portal.renew') }}" method="POST" style="border-top:1px solid rgba(255,255,255,0.08);padding-top:14px;">
                        @csrf
                        <input type="hidden" name="member_id" value="{{ $member->id }}">
                        <div style="font-size:12px;font-weight:700;color:#CBD5E1;margin-bottom:8px;">Renew or Upgrade Package:</div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                            <select name="package_id" class="gym-select" style="height:36px;font-size:12px;">
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg->id }}" {{ $member->package_id === $pkg->id ? 'selected' : '' }}>
                                        {{ $pkg->name }} (${{ (float) $pkg->monthly_price }}/mo)
                                    </option>
                                @endforeach
                            </select>
                            <select name="billing_cycle" class="gym-select" style="height:36px;font-size:12px;">
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="six_month">6 Months</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <input type="hidden" name="payment_method" value="bKash / Online Portal">
                        <button type="submit" class="gym-btn-outline" style="width:100%;padding:8px 14px;font-size:12.5px;">
                            <i class="fa-solid fa-arrows-rotate"></i> Renew / Upgrade Membership
                        </button>
                    </form>
                </div>
            </div>

            {{-- Row 2: Assigned Workout & Diet Plans + Body Measurement Progress Logger --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:28px;">
                {{-- Assigned Workout & Diet Plans --}}
                <div style="background:#121912;border:1px solid rgba(255,255,255,0.08);border-radius:18px;padding:24px;">
                    <h3 style="font-size:18px;font-weight:800;color:#fff;margin-bottom:14px;">
                        <i class="fa-solid fa-clipboard-list" style="color:var(--gym-primary);margin-right:8px;"></i>
                        Assigned Workout & Nutrition Plans
                    </h3>

                    <div style="background:#0B100B;border:1px solid rgba(163,230,53,0.2);border-radius:14px;padding:16px;margin-bottom:12px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                            <span style="font-size:11px;font-weight:800;color:var(--gym-primary);text-transform:uppercase;">WORKOUT PLAN</span>
                            <span style="font-size:11.5px;color:#9CA3AF;">{{ $member->workoutPlan?->duration_weeks ?? 8 }} Weeks • {{ $member->workoutPlan?->level ?? 'All Levels' }}</span>
                        </div>
                        <h4 style="font-size:15px;font-weight:800;color:#fff;margin-bottom:6px;">
                            {{ $member->workoutPlan ? ($isBn ? ($member->workoutPlan->title_bn ?: $member->workoutPlan->title) : $member->workoutPlan->title) : 'Full Body Strength & Conditioning' }}
                        </h4>
                        <p style="font-size:12.5px;color:#CBD5E1;margin:0;">
                            {{ $member->workoutPlan ? ($isBn ? ($member->workoutPlan->schedule_details_bn ?: $member->workoutPlan->schedule_details) : $member->workoutPlan->schedule_details) : '4 Days/Week progressive overload + 2 Days HIIT conditioning.' }}
                        </p>
                    </div>

                    <div style="background:#0B100B;border:1px solid rgba(163,230,53,0.2);border-radius:14px;padding:16px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                            <span style="font-size:11px;font-weight:800;color:var(--gym-primary);text-transform:uppercase;">DIET & NUTRITION PLAN</span>
                            <span style="font-size:11.5px;color:#9CA3AF;">{{ $member->dietPlan?->calories_target ?? '2,200 kcal / day' }}</span>
                        </div>
                        <h4 style="font-size:15px;font-weight:800;color:#fff;margin-bottom:6px;">
                            {{ $member->dietPlan ? ($isBn ? ($member->dietPlan->title_bn ?: $member->dietPlan->title) : $member->dietPlan->title) : 'Balanced High-Protein Nutrition Guide' }}
                        </h4>
                        <p style="font-size:12.5px;color:#CBD5E1;margin:0;">
                            {{ $member->dietPlan ? ($isBn ? ($member->dietPlan->schedule_details_bn ?: $member->dietPlan->schedule_details) : $member->dietPlan->schedule_details) : 'High protein whole-food meals with 3.5L daily hydration.' }}
                        </p>
                    </div>
                </div>

                {{-- Fitness Progress & Body Measurements Tracker --}}
                <div style="background:#121912;border:1px solid rgba(255,255,255,0.08);border-radius:18px;padding:24px;">
                    <h3 style="font-size:18px;font-weight:800;color:#fff;margin-bottom:14px;">
                        <i class="fa-solid fa-weight-scale" style="color:var(--gym-primary);margin-right:8px;"></i>
                        Body Measurements & Weight Progress
                    </h3>

                    <form action="{{ route('gymfitness.portal.log_progress') }}" method="POST" style="margin-bottom:16px;">
                        @csrf
                        <input type="hidden" name="member_id" value="{{ $member->id }}">
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:8px;">
                            <input type="number" step="0.1" name="weight_kg" class="gym-input" style="height:36px;font-size:12px;" placeholder="Weight (kg) *" required>
                            <input type="number" step="0.1" name="body_fat_pct" class="gym-input" style="height:36px;font-size:12px;" placeholder="Body Fat %">
                            <input type="number" step="0.1" name="waist_cm" class="gym-input" style="height:36px;font-size:12px;" placeholder="Waist (cm)">
                            <input type="number" step="0.1" name="chest_cm" class="gym-input" style="height:36px;font-size:12px;" placeholder="Chest (cm)">
                            <input type="number" step="0.1" name="arms_cm" class="gym-input" style="height:36px;font-size:12px;" placeholder="Arms (cm)">
                            <button type="submit" class="gym-btn-lime" style="height:36px;font-size:12px;padding:0 12px;">+ Log Progress</button>
                        </div>
                    </form>

                    <div style="max-height:175px;overflow-y:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:12.5px;">
                            <thead>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.1);color:#9CA3AF;text-align:left;">
                                    <th style="padding:6px;">Date</th>
                                    <th style="padding:6px;">Weight</th>
                                    <th style="padding:6px;">BMI</th>
                                    <th style="padding:6px;">Body Fat</th>
                                    <th style="padding:6px;">Waist</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($progressLogs as $log)
                                    <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                        <td style="padding:6px;color:#CBD5E1;">{{ $log->recorded_date?->format('M d, Y') }}</td>
                                        <td style="padding:6px;font-weight:800;color:var(--gym-primary);">{{ $log->weight_kg }} kg</td>
                                        <td style="padding:6px;">{{ $log->bmi ?: '—' }}</td>
                                        <td style="padding:6px;">{{ $log->body_fat_pct ? $log->body_fat_pct . '%' : '—' }}</td>
                                        <td style="padding:6px;">{{ $log->waist_cm ? $log->waist_cm . ' cm' : '—' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" style="padding:10px;color:#6B7280;">No body measurement logs yet. Log your first entry above!</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Row 3: Class Booking & Schedule + Attendance & Payment History --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;">
                {{-- Class Booking & My Booked Classes --}}
                <div style="background:#121912;border:1px solid rgba(255,255,255,0.08);border-radius:18px;padding:24px;">
                    <h3 style="font-size:18px;font-weight:800;color:#fff;margin-bottom:14px;">
                        <i class="fa-solid fa-calendar-plus" style="color:var(--gym-primary);margin-right:8px;"></i>
                        Book a Group Class
                    </h3>

                    <form action="{{ route('gymfitness.portal.book_class') }}" method="POST" style="display:flex;gap:8px;margin-bottom:18px;">
                        @csrf
                        <input type="hidden" name="member_id" value="{{ $member->id }}">
                        <select name="class_id" class="gym-select" style="flex:1;height:40px;font-size:12.5px;" required>
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}">
                                    {{ $cls->day_of_week }} ({{ $cls->time_slot }}) — {{ $cls->title }} [{{ $cls->trainer?->name ?? 'Coach' }}] ({{ $cls->booked_count }}/{{ $cls->max_participants }})
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="gym-btn-lime" style="height:40px;padding:0 16px;font-size:12.5px;">Book Class</button>
                    </form>

                    <h4 style="font-size:13.5px;font-weight:700;color:#CBD5E1;margin-bottom:8px;">My Class Bookings</h4>
                    <table style="width:100%;border-collapse:collapse;font-size:12.5px;">
                        <thead>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.1);color:#9CA3AF;text-align:left;">
                                <th style="padding:7px;">Class</th>
                                <th style="padding:7px;">Day & Time</th>
                                <th style="padding:7px;">Status</th>
                                <th style="padding:7px;text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $b)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                    <td style="padding:7px;font-weight:700;color:#fff;">{{ $b->gymClass?->title ?? 'Class' }}</td>
                                    <td style="padding:7px;color:#9CA3AF;">{{ $b->gymClass?->day_of_week }} • {{ $b->gymClass?->time_slot }}</td>
                                    <td style="padding:7px;">
                                        <span style="color:{{ $b->status === 'Cancelled' ? '#F87171' : 'var(--gym-primary)' }};font-weight:700;">{{ $b->status }}</span>
                                    </td>
                                    <td style="padding:7px;text-align:right;">
                                        @if($b->status === 'Booked')
                                            <form action="{{ route('gymfitness.portal.cancel_class', $b->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" style="background:none;border:none;color:#F87171;font-size:12px;cursor:pointer;font-weight:700;">Cancel</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="padding:10px;color:#6B7280;">No classes booked yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Payment History & Recent Check-In Attendance --}}
                <div style="background:#121912;border:1px solid rgba(255,255,255,0.08);border-radius:18px;padding:24px;">
                    <h3 style="font-size:18px;font-weight:800;color:#fff;margin-bottom:14px;">
                        <i class="fa-solid fa-file-invoice-dollar" style="color:var(--gym-primary);margin-right:8px;"></i>
                        Invoices, Payments & Check-In Logs
                    </h3>

                    <table style="width:100%;border-collapse:collapse;font-size:12.5px;margin-bottom:18px;">
                        <thead>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.1);color:#9CA3AF;text-align:left;">
                                <th style="padding:6px;">Invoice</th>
                                <th style="padding:6px;">Type</th>
                                <th style="padding:6px;">Amount</th>
                                <th style="padding:6px;">Method</th>
                                <th style="padding:6px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $pay)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                    <td style="padding:6px;font-weight:700;color:#fff;">{{ $pay->invoice_no }}</td>
                                    <td style="padding:6px;color:#CBD5E1;">{{ $pay->payment_type }}</td>
                                    <td style="padding:6px;color:var(--gym-primary);font-weight:800;">${{ number_format((float) $pay->amount, 2) }}</td>
                                    <td style="padding:6px;color:#9CA3AF;">{{ $pay->payment_method }}</td>
                                    <td style="padding:6px;">
                                        <span style="color:{{ $pay->payment_status === 'Paid' ? 'var(--gym-primary)' : '#FBBF24' }};font-weight:700;">{{ $pay->payment_status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" style="padding:8px;color:#6B7280;">No invoices recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <h4 style="font-size:13.5px;font-weight:700;color:#CBD5E1;margin-bottom:8px;">Recent Gym Check-In History</h4>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @forelse($attendances as $att)
                            <span style="background:#0B100B;border:1px solid rgba(163,230,53,0.22);border-radius:10px;padding:6px 12px;font-size:12px;color:#E2E8F0;">
                                <i class="fa-solid fa-circle-check" style="color:var(--gym-primary);margin-right:4px;"></i>
                                {{ $att->attendance_date?->format('M d') }} ({{ $att->check_in_time }}) • {{ $att->check_in_method }}
                            </span>
                        @empty
                            <span style="font-size:12px;color:#6B7280;">No check-ins recorded yet.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
