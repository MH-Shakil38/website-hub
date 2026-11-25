@extends('admin.layouts.master')

@section('title', 'Resellers & Clients - SiteBuilder')

@section('admin_content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0F172A;">
                @if($user->isSuperAdmin())
                    Resellers &amp; Client Accounts
                @else
                    My Client Accounts
                @endif
            </h4>
            <p class="text-muted small mb-0">
                Manage accounts and use one-click <strong>Impersonate</strong> to open their dashboard panel and configure their websites.
            </p>
        </div>
        <button type="button" class="btn btn-danger fw-semibold px-3 py-2" data-bs-toggle="modal" data-bs-target="#createAccountModal" style="background: #EC134A; border: none; border-radius: 10px; font-size: 13px;">
            <i class="bi bi-person-plus-fill me-1"></i>
            {{ $user->isSuperAdmin() ? 'Add Reseller / Client' : 'Add New Client' }}
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-body p-3">
            <form action="{{ route('admin.saas.accounts') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search by name or email...">
                </div>
                @if($user->isSuperAdmin())
                    <div class="col-md-4">
                        <select name="type" class="form-select form-select-sm">
                            <option value="">All Roles (Super Admin, Reseller, Client)</option>
                            <option value="super_admin" {{ $roleFilter === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="reseller" {{ $roleFilter === 'reseller' ? 'selected' : '' }}>Reseller Partners</option>
                            <option value="client" {{ $roleFilter === 'client' ? 'selected' : '' }}>Clients</option>
                        </select>
                    </div>
                @endif
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100">Filter</button>
                    <a href="{{ route('admin.saas.accounts') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 14px;">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 13px;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Name &amp; Email</th>
                        <th>SaaS Panel Role</th>
                        <th>Referred / Managed By</th>
                        <th>Websites (Tenants)</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Impersonate / Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $acc)
                        <tr>
                            <td class="ps-4 fw-bold">#{{ $acc->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $acc->name }}</div>
                                <div class="text-muted" style="font-size: 11.5px;">{{ $acc->email }}</div>
                            </td>
                            <td>
                                @php $pType = $acc->panelType(); @endphp
                                @if($pType === 'super_admin')
                                    <span class="badge" style="background: #FFE4EC; color: #EC134A;">SUPER ADMIN</span>
                                @elseif($pType === 'reseller')
                                    <span class="badge" style="background: #DBEAFE; color: #2563EB;">RESELLER PARTNER</span>
                                @else
                                    <span class="badge" style="background: #DCFCE7; color: #10B981;">CLIENT</span>
                                @endif
                            </td>
                            <td>
                                @if($acc->reseller)
                                    <span class="fw-semibold">{{ $acc->reseller->name }}</span>
                                    <small class="text-muted d-block" style="font-size: 10.5px;">via {{ $acc->referred_via ?? 'reseller' }}</small>
                                @else
                                    <span class="text-muted small">Direct ({{ $globalSiteName ?? 'YourHub' }})</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $acc->tenants->count() }} Websites</span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success">{{ $acc->status ?? 'Active' }}</span>
                            </td>
                            <td class="text-end pe-4">
                                @if((int) $acc->id !== (int) $user->id && ($user->isSuperAdmin() || ($user->isReseller() && (int) $acc->reseller_id === (int) $user->id)))
                                    <form action="{{ route('admin.impersonate.start', $acc->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-dark fw-semibold" style="font-size: 11.5px; border-radius: 7px;">
                                            <i class="bi bi-box-arrow-in-right me-1"></i> Login as {{ ucfirst($acc->panelType()) }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">Current User</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($accounts->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3">
                {{ $accounts->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Create Reseller or Client Account -->
    <div class="modal fade" id="createAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.saas.accounts.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">{{ $user->isSuperAdmin() ? 'Create Reseller or Client Account' : 'Create Client Account' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Full Name / Business Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Karim Agency or Client Name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="client@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="+8801XXXXXXXXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" value="password" required>
                    </div>
                    @if($user->isSuperAdmin())
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Account Role Type</label>
                            <select name="type" class="form-select">
                                <option value="client">Client (Website Owner)</option>
                                <option value="reseller">Reseller Partner (Sells under YourHub brand)</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Assign Under Reseller (Optional, for Clients)</label>
                            <select name="reseller_id" class="form-select">
                                <option value="">Direct Platform Client</option>
                                @foreach($resellers as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="type" value="client">
                    @endif
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-semibold" style="background: #EC134A; border: none;">
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
