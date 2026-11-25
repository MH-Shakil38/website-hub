@extends('admin.layouts.master')

@section('title', 'Members & Subscriptions — FitPulse Gym Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Member Management &amp; Lifecycle</h3>
                <span class="sb-badge-pill sb-badge-green">gym_members</span>
            </div>
            <p class="text-muted small mb-0">Register members, assign packages &amp; trainers, and manage renewals, upgrades, freezes, and suspensions.</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form action="{{ route('admin.gymfitness.members.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                <select name="package_id" class="form-select form-select-sm" style="width:150px;" onchange="this.form.submit()">
                    <option value="">All Packages</option>
                    @foreach($packages as $p)
                        <option value="{{ $p->id }}" {{ (string) $packageId === (string) $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-select form-select-sm" style="width:135px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    @foreach(['Active', 'Expired', 'Frozen', 'Suspended', 'Pending'] as $st)
                        <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Name, ID, Phone..." style="width:180px;">
            </form>

            <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                <i class="bi bi-person-plus-fill me-1"></i> Register Member
            </button>
        </div>
    </div>

    <div class="sb-table-card">
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Member &amp; ID</th>
                        <th>Contact &amp; Emergency</th>
                        <th>Package &amp; Cycle</th>
                        <th>Assigned Trainer / Plans</th>
                        <th>Expiration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $m)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $m->photo ?: '/module-media/GymFitness/theme/trainer-1.jpg' }}"
                                         alt="{{ $m->name }}"
                                         style="width:46px;height:46px;border-radius:50%;object-fit:cover;border:2px solid #84CC16;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $m->name }}</div>
                                        <div class="small text-primary fw-semibold">{{ $m->member_code }} • {{ $m->gender }} ({{ $m->blood_group }})</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $m->phone }}</div>
                                <div class="text-muted small">{{ $m->email }}</div>
                                @if($m->emergency_contact_phone)
                                    <div class="text-danger small" style="font-size:11px;">SOS: {{ $m->emergency_contact_name }} ({{ $m->emergency_contact_phone }})</div>
                                @endif
                            </td>
                            <td>
                                <span class="sb-badge-pill sb-badge-blue">{{ $m->package?->name ?? 'Basic Plan' }}</span>
                                <div class="text-muted small mt-1 text-capitalize">{{ str_replace('_', ' ', $m->billing_cycle) }}</div>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $m->trainer?->name ?? 'Floor Coach' }}</div>
                                <div class="text-muted small" style="font-size:11px;">
                                    W: {{ Str::limit($m->workoutPlan?->title ?? 'Default', 18) }} | D: {{ Str::limit($m->dietPlan?->title ?? 'Default', 18) }}
                                </div>
                            </td>
                            <td>
                                <div class="small fw-bold {{ $m->expiry_date && $m->expiry_date->isPast() ? 'text-danger' : 'text-dark' }}">
                                    {{ $m->expiry_date ? $m->expiry_date->format('M d, Y') : 'N/A' }}
                                </div>
                                <div class="text-muted small">Joined: {{ $m->join_date?->format('M d, Y') }}</div>
                            </td>
                            <td>
                                <span class="sb-badge-pill {{ $m->membership_status === 'Active' ? 'sb-badge-green' : ($m->membership_status === 'Expired' ? 'sb-badge-amber' : 'sb-badge-blue') }}">
                                    {{ $m->membership_status }}
                                </span>
                                <div class="small mt-1 {{ $m->payment_status === 'Paid' ? 'text-success' : 'text-warning' }} fw-bold">
                                    {{ $m->payment_status }} @if($m->due_amount > 0) (${{ (float) $m->due_amount }} Due) @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('gymfitness.portal', ['member_id' => $m->id]) }}" target="_blank" class="sb-page-btn text-success" title="Open Member Portal & QR Pass">
                                        <i class="bi bi-qr-code"></i>
                                    </a>
                                    <button type="button" class="sb-page-btn text-primary" data-bs-toggle="modal" data-bs-target="#lifecycleMember{{ $m->id }}" title="Renew / Upgrade / Freeze / Suspend">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editMember{{ $m->id }}" title="Edit Member">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.gymfitness.members.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Delete member {{ $m->name }}?')">
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
        <div class="p-3">{{ $members->links() }}</div>
    </div>

    {{-- Lifecycle & Edit Modals for Each Member --}}
    @foreach($members as $m)
        {{-- 1. Membership Lifecycle Action Modal --}}
        <div class="modal fade" id="lifecycleMember{{ $m->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Membership Lifecycle &amp; History — {{ $m->name }} ({{ $m->member_code }})</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form action="{{ route('admin.gymfitness.members.lifecycle', $m->id) }}" method="POST" class="border rounded-3 p-3 bg-light mb-4">
                            @csrf
                            <h6 class="fw-bold mb-3">Execute Membership Action</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Action Type</label>
                                    <select name="action_type" class="form-select" required>
                                        <option value="Renewal">Renew Membership</option>
                                        <option value="Upgrade">Upgrade Package</option>
                                        <option value="Downgrade">Downgrade Package</option>
                                        <option value="Freeze">Freeze Membership</option>
                                        <option value="Suspend">Suspend Membership</option>
                                        <option value="Activate">Re-Activate Membership</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Target Package</label>
                                    <select name="package_id" class="form-select">
                                        @foreach($packages as $p)
                                            <option value="{{ $p->id }}" {{ $m->package_id === $p->id ? 'selected' : '' }}>
                                                {{ $p->name }} (${{ (float) $p->monthly_price }}/mo)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Billing Duration</label>
                                    <select name="billing_cycle" class="form-select">
                                        <option value="monthly">Monthly (1 Month)</option>
                                        <option value="quarterly">Quarterly (3 Months)</option>
                                        <option value="six_month">Six-Month (6 Months)</option>
                                        <option value="yearly">Yearly (12 Months)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-3 text-end">
                                <button type="submit" class="btn btn-success btn-sm fw-bold px-4">Apply Action</button>
                            </div>
                        </form>

                        <h6 class="fw-bold mb-2">Membership Subscription History</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered small mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Action</th>
                                        <th>Plan</th>
                                        <th>Cycle</th>
                                        <th>Amount</th>
                                        <th>Start &rarr; End</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($m->subscriptions as $sub)
                                        <tr>
                                            <td class="fw-bold">{{ $sub->action_type }}</td>
                                            <td>{{ $sub->plan_name }}</td>
                                            <td>{{ $sub->billing_cycle }}</td>
                                            <td>${{ number_format((float) $sub->amount, 2) }}</td>
                                            <td>{{ $sub->start_date?->format('M d, Y') }} &rarr; {{ $sub->end_date?->format('M d, Y') }}</td>
                                            <td>{{ $sub->status }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-muted text-center">No subscription history recorded.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Edit Member Modal --}}
        <div class="modal fade" id="editMember{{ $m->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                    <form action="{{ route('admin.gymfitness.members.update', $m->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-bottom px-4 py-3">
                            <h5 class="modal-title fw-bold">Edit Member Profile: {{ $m->name }} ({{ $m->member_code }})</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Full Name *</label>
                                    <input type="text" name="name" value="{{ $m->name }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Email *</label>
                                    <input type="email" name="email" value="{{ $m->email }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Phone *</label>
                                    <input type="text" name="phone" value="{{ $m->phone }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Date of Birth</label>
                                    <input type="date" name="date_of_birth" value="{{ $m->date_of_birth?->format('Y-m-d') }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Gender</label>
                                    <select name="gender" class="form-select">
                                        @foreach(['Male', 'Female', 'Other'] as $g)
                                            <option value="{{ $g }}" {{ $m->gender === $g ? 'selected' : '' }}>{{ $g }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Blood Group</label>
                                    <input type="text" name="blood_group" value="{{ $m->blood_group }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Emergency Contact Name</label>
                                    <input type="text" name="emergency_contact_name" value="{{ $m->emergency_contact_name }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Emergency Contact Phone</label>
                                    <input type="text" name="emergency_contact_phone" value="{{ $m->emergency_contact_phone }}" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Address</label>
                                    <input type="text" name="address" value="{{ $m->address }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Membership Package</label>
                                    <select name="package_id" class="form-select">
                                        @foreach($packages as $p)
                                            <option value="{{ $p->id }}" {{ $m->package_id === $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Assigned Trainer</label>
                                    <select name="trainer_id" class="form-select">
                                        <option value="">General Floor Coach</option>
                                        @foreach($trainers as $tr)
                                            <option value="{{ $tr->id }}" {{ $m->trainer_id === $tr->id ? 'selected' : '' }}>{{ $tr->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Billing Cycle</label>
                                    <select name="billing_cycle" class="form-select">
                                        @foreach(['monthly', 'quarterly', 'six_month', 'yearly', 'custom'] as $bc)
                                            <option value="{{ $bc }}" {{ $m->billing_cycle === $bc ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $bc)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Assigned Workout Plan</label>
                                    <select name="workout_plan_id" class="form-select">
                                        <option value="">Default Workout Plan</option>
                                        @foreach($workoutPlans as $wp)
                                            <option value="{{ $wp->id }}" {{ $m->workout_plan_id === $wp->id ? 'selected' : '' }}>{{ $wp->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Assigned Diet Plan</label>
                                    <select name="diet_plan_id" class="form-select">
                                        <option value="">Default Diet Plan</option>
                                        @foreach($dietPlans as $dp)
                                            <option value="{{ $dp->id }}" {{ $m->diet_plan_id === $dp->id ? 'selected' : '' }}>{{ $dp->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Expiry Date</label>
                                    <input type="date" name="expiry_date" value="{{ $m->expiry_date?->format('Y-m-d') }}" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Membership Status</label>
                                    <select name="membership_status" class="form-select">
                                        @foreach(['Active', 'Expired', 'Suspended', 'Frozen', 'Pending'] as $ms)
                                            <option value="{{ $ms }}" {{ $m->membership_status === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Payment Status</label>
                                    <select name="payment_status" class="form-select">
                                        @foreach(['Paid', 'Partial', 'Due', 'Pending'] as $ps)
                                            <option value="{{ $ps }}" {{ $m->payment_status === $ps ? 'selected' : '' }}>{{ $ps }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Profile Photo</label>
                                    <input type="file" name="photo_file" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-bold px-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Register New Member Modal --}}
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                <form action="{{ route('admin.gymfitness.members.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Register New Gym Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Full Name *</label>
                                <input type="text" name="name" class="form-control" required placeholder="Member Full Name">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Email *</label>
                                <input type="email" name="email" class="form-control" required placeholder="member@example.com">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Phone *</label>
                                <input type="text" name="phone" class="form-control" required placeholder="+880 1711-000000">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Gender *</label>
                                <select name="gender" class="form-select" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Blood Group</label>
                                <input type="text" name="blood_group" value="O+" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="form-control" placeholder="Parent / Spouse Name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Emergency Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" class="form-control" placeholder="+880 1811-000000">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Address</label>
                                <input type="text" name="address" class="form-control" placeholder="House, Road, Area, Dhaka">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Membership Package *</label>
                                <select name="package_id" class="form-select" required>
                                    @foreach($packages as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} (${{ (float) $p->monthly_price }}/mo)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Billing Duration *</label>
                                <select name="billing_cycle" class="form-select" required>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="six_month">6 Months</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Assigned Trainer</label>
                                <select name="trainer_id" class="form-select">
                                    <option value="">General Floor Coach</option>
                                    @foreach($trainers as $tr)
                                        <option value="{{ $tr->id }}">{{ $tr->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Membership Status</label>
                                <select name="membership_status" class="form-select">
                                    <option value="Active">Active</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Payment Status</label>
                                <select name="payment_status" class="form-select">
                                    <option value="Paid">Paid</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Profile Photo</label>
                                <input type="file" name="photo_file" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold px-4">Register Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
