@extends('admin.layouts.master')

@section('title', 'SaaS Tenants & Databases - SiteBuilder')

@section('admin_content')
    @include('admin.partials.module-switcher')

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0F172A;">
                @if($user->isSuperAdmin())
                    SaaS Tenants &amp; Isolated Databases
                @elseif($user->isReseller())
                    Client Websites &amp; Databases
                @else
                    My Websites &amp; Domains
                @endif
            </h4>
            <p class="text-muted small mb-0">
                Every website runs in its own isolated MySQL database (<code>hub_t_XXXX</code>) with dedicated theme tables and uploads folder.
            </p>
        </div>
        <button type="button" class="btn btn-danger fw-semibold px-3 py-2" data-bs-toggle="modal" data-bs-target="#createTenantModal" style="background: #EC134A; border: none; border-radius: 10px; font-size: 13px;">
            <i class="bi bi-plus-lg me-1"></i> Provision New Website (Tenant DB)
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-body p-3">
            <form action="{{ route('admin.tenants.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search by site name, subdomain, or database (e.g. hub_t_0001)...">
                </div>
                <div class="col-md-3">
                    <select name="theme" class="form-select form-select-sm">
                        <option value="">All Theme Modules</option>
                        @foreach($themes as $modKey => $meta)
                            <option value="{{ $modKey }}" {{ $themeFilter === $modKey ? 'selected' : '' }}>{{ $meta['label'] }} ({{ $modKey }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="trial" {{ $statusFilter === 'trial' ? 'selected' : '' }}>Free Trial</option>
                        <option value="suspended" {{ $statusFilter === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100">Filter</button>
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tenants Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 14px;">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 13px;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Website Name</th>
                        <th>Theme Module</th>
                        <th>Subdomain / Primary Domain</th>
                        <th>Isolated Database</th>
                        <th>Owner</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $currentActiveTenant = function_exists('current_tenant') ? current_tenant() : null;
                        $baseCentralDomain = config('app.central_domains')[0] ?? 'localhost';
                    @endphp
                    @forelse($tenants as $t)
                        @php
                            $isCurrentContext = $currentActiveTenant && (int) $currentActiveTenant->id === (int) $t->id;
                        @endphp
                        <tr class="{{ $isCurrentContext ? 'table-warning' : '' }}">
                            <td class="ps-4 fw-bold">#{{ $t->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">
                                    {{ $t->name }}
                                    @if($t->is_demo)
                                        <span class="badge bg-secondary-subtle text-secondary ms-1" style="font-size: 10px;">DEMO</span>
                                    @endif
                                    @if($isCurrentContext)
                                        <span class="badge bg-danger ms-1" style="font-size: 10px;">CONNECTED</span>
                                    @endif
                                </div>
                                <div class="text-muted" style="font-size: 11px;">UUID: {{ Str::limit($t->uuid, 13) }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $t->theme_module }}</span>
                            </td>
                            <td>
                                <code>{{ $t->subdomain }}.{{ $baseCentralDomain }}</code>
                            </td>
                            <td>
                                <code class="px-2 py-1 rounded" style="background: #DCFCE7; color: #166534; font-weight: 600;">
                                    <i class="bi bi-database-fill-check me-1"></i>{{ $t->db_name ?? 'Pending' }}
                                </code>
                            </td>
                            <td>
                                @if($t->owner)
                                    <div class="fw-semibold">{{ $t->owner->name }}</div>
                                    <div class="text-muted" style="font-size: 11px;">{{ $t->owner->email }}</div>
                                @else
                                    <span class="text-muted small">Platform Demo</span>
                                @endif
                            </td>
                            <td>
                                @if($t->status === 'active')
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @elseif($t->status === 'trial')
                                    <span class="badge bg-info-subtle text-info">Trial</span>
                                @elseif($t->status === 'suspended')
                                    <span class="badge bg-danger-subtle text-danger">Suspended</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">{{ ucfirst($t->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('admin.tenants.switch', $t->id) }}"
                                       class="btn btn-sm {{ $isCurrentContext ? 'btn-danger' : 'btn-outline-danger' }} fw-semibold"
                                       style="font-size: 11.5px; border-radius: 7px;">
                                        <i class="bi bi-sliders me-1"></i> {{ $isCurrentContext ? 'Managing CMS' : 'Manage CMS' }}
                                    </a>

                                    @if($user->isSuperAdmin() || ($user->isReseller() && (int) $t->reseller_id === (int) $user->id))
                                        <form action="{{ route('admin.tenants.toggle-status', $t->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Toggle Active/Suspended" style="font-size: 11.5px; border-radius: 7px;">
                                                <i class="bi {{ in_array($t->status, ['active', 'trial']) ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                            </button>
                                        </form>

                                        @if(!$t->is_demo)
                                            <form action="{{ route('admin.tenants.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Drop database {{ $t->db_name }} and delete this tenant?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Tenant & Drop DB" style="font-size: 11.5px; border-radius: 7px;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No tenant websites found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tenants->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Provision New Website (Tenant + Isolated DB) -->
    <div class="modal fade" id="createTenantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.tenants.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Provision New Website (Isolated DB)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Website / Business Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Royal Interior Studio" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Theme Module</label>
                        <select name="theme_module" class="form-select" required>
                            @foreach($themes as $modKey => $meta)
                                <option value="{{ $modKey }}">{{ $meta['label'] }} ({{ $modKey }} - {{ $meta['prefix'] }}*)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Subdomain</label>
                        <div class="input-group">
                            <input type="text" name="subdomain" class="form-control" placeholder="royal-studio" pattern="[a-zA-Z0-9\-]+" required>
                            <span class="input-group-text">.{{ config('app.central_domains')[0] ?? 'localhost' }}</span>
                        </div>
                        <small class="text-muted" style="font-size: 11px;">Only letters, numbers, and hyphens.</small>
                    </div>
                    @if(!$user->isClient())
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Assign to Client Owner</label>
                            <select name="owner_id" class="form-select">
                                <option value="{{ $user->id }}">Myself ({{ $user->name }})</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->email }} - {{ strtoupper($c->type) }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Initial Status</label>
                        <select name="status" class="form-select">
                            <option value="trial">14-Day Free Trial</option>
                            <option value="active">Active Subscription</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-semibold" style="background: #EC134A; border: none;">
                        <i class="bi bi-hdd-network me-1"></i> Create Database &amp; Install Theme
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
