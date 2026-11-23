@extends('admin.layouts.master')

@section('title', 'Domains, DNS Verification & SSL - SiteBuilder')

@section('admin_content')
    @php
        $serverIp = config('app.server_ip', '127.0.0.1');
        $cnameTarget = config('app.cname_target', 'sites.multi-website-hub.test');
    @endphp

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0F172A;">Domains, DNS Verification &amp; Auto-SSL</h4>
            <p class="text-muted small mb-0">
                Connect custom domains to your websites, verify DNS ownership, set a primary domain (auto 301 redirect), and authorize Caddy On-Demand SSL.
            </p>
        </div>
        <button type="button" class="btn btn-danger fw-semibold px-3 py-2" data-bs-toggle="modal" data-bs-target="#addCustomDomainModal" style="background: #EC134A; border: none; border-radius: 10px; font-size: 13px;">
            <i class="bi bi-plus-lg me-1"></i> Connect Custom Domain
        </button>
    </div>

    <!-- DNS Instructions Banner -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: #0F172A; color: #FFFFFF;">
        <div class="card-body p-3 px-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <div class="fw-bold mb-1" style="font-size: 13.5px;">
                        <i class="bi bi-shield-lock-fill text-warning me-1"></i> How to Connect a Custom Domain (DNS Configuration)
                    </div>
                    <div class="text-white-50" style="font-size: 12px;">
                        Point your domain's <strong>A Record</strong> (<code>@</code>) to <code class="text-info">{{ $serverIp }}</code>
                        OR point a <strong>CNAME Record</strong> to <code class="text-info">{{ $cnameTarget }}</code>,
                        and add the <strong>TXT Verification Token</strong> shown in the table below. Once verified, Caddy On-Demand TLS automatically issues a free Let's Encrypt SSL certificate.
                    </div>
                </div>
                <span class="badge bg-success px-3 py-2 rounded-pill" style="font-size: 11px;">
                    <i class="bi bi-lock-fill me-1"></i> Caddy On-Demand TLS Ready
                </span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-body p-3">
            <form action="{{ route('admin.domains.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search domain name (e.g. clientshop.com)...">
                </div>
                <div class="col-md-5">
                    <select name="tenant_id" class="form-select form-select-sm">
                        <option value="">All Accessible Websites ({{ $accessibleTenants->count() }})</option>
                        @foreach($accessibleTenants as $tOpt)
                            <option value="{{ $tOpt->id }}" {{ (string) $selectedTenantId === (string) $tOpt->id ? 'selected' : '' }}>
                                {{ $tOpt->name }} ({{ $tOpt->theme_module }} - {{ $tOpt->db_name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100">Filter</button>
                    <a href="{{ route('admin.domains.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Domains Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 14px;">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 13px;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Domain / Hostname</th>
                        <th>Type</th>
                        <th>Connected Website (Tenant DB)</th>
                        <th>DNS Verification / Token</th>
                        <th>SSL Status</th>
                        <th>Routing Role</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($domains as $d)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">
                                    {{ $d->domain }}
                                    @if($d->is_primary)
                                        <span class="badge bg-danger ms-1" style="font-size: 10px;">PRIMARY</span>
                                    @endif
                                </div>
                                <div class="text-muted" style="font-size: 11px;">
                                    Added {{ $d->created_at?->diffForHumans() ?? 'recently' }}
                                </div>
                            </td>
                            <td>
                                @if($d->type === 'custom')
                                    <span class="badge bg-primary-subtle text-primary">Custom Domain</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Free Subdomain</span>
                                @endif
                            </td>
                            <td>
                                @if($d->tenant)
                                    <div class="fw-semibold">{{ $d->tenant->name }}</div>
                                    <div class="text-muted" style="font-size: 11px;">
                                        {{ $d->tenant->theme_module }} • <code>{{ $d->tenant->db_name }}</code>
                                    </div>
                                @else
                                    <span class="text-muted">Orphaned</span>
                                @endif
                            </td>
                            <td>
                                @if($d->verified_at)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle-fill me-1"></i> Verified
                                    </span>
                                    <div class="text-muted" style="font-size: 10.5px;">{{ $d->verified_at->format('M d, Y H:i') }}</div>
                                @else
                                    <span class="badge bg-warning-subtle text-warning mb-1">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Pending DNS
                                    </span>
                                    @if($d->verification_token)
                                        <div style="font-size: 11px;">
                                            TXT (<code>_yourhub-challenge</code>):
                                            <code class="d-block text-dark bg-light p-1 rounded border mt-1" style="font-size: 10.5px; user-select: all;">{{ $d->verification_token }}</code>
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($d->ssl_status === 'active')
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-lock-fill me-1"></i> SSL Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="bi bi-hourglass-split me-1"></i> {{ ucfirst($d->ssl_status) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($d->is_primary)
                                    <span class="text-success fw-semibold" style="font-size: 12px;">
                                        <i class="bi bi-star-fill me-1"></i> Primary Canonical URL
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: 12px;">
                                        301 Redirects to Primary
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex align-items-center gap-1">
                                    @if(!$d->verified_at)
                                        <form action="{{ route('admin.domains.verify', $d->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @if(app()->environment('local'))
                                                <input type="hidden" name="simulate_local" value="1">
                                            @endif
                                            <button type="submit" class="btn btn-sm btn-success fw-semibold" style="font-size: 11.5px; border-radius: 7px;">
                                                <i class="bi bi-shield-check me-1"></i> Verify DNS
                                            </button>
                                        </form>
                                    @endif

                                    @if($d->verified_at && !$d->is_primary)
                                        <form action="{{ route('admin.domains.primary', $d->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-dark fw-semibold" style="font-size: 11.5px; border-radius: 7px;">
                                                Make Primary
                                            </button>
                                        </form>
                                    @endif

                                    @if($d->type === 'custom')
                                        <form action="{{ route('admin.domains.destroy', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Disconnect custom domain {{ $d->domain }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size: 11.5px; border-radius: 7px;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No domains found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($domains->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3">
                {{ $domains->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Connect Custom Domain -->
    <div class="modal fade" id="addCustomDomainModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.domains.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Connect Custom Domain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Select Target Website</label>
                        <select name="tenant_id" class="form-select" required>
                            @foreach($accessibleTenants as $tOpt)
                                <option value="{{ $tOpt->id }}" {{ (string) $selectedTenantId === (string) $tOpt->id ? 'selected' : '' }}>
                                    {{ $tOpt->name }} ({{ $tOpt->theme_module }} • {{ $tOpt->subdomain }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Custom Domain Name</label>
                        <input type="text" name="domain" class="form-control" placeholder="e.g. royalinteriors.com or shop.clientbrand.com" required>
                        <small class="text-muted" style="font-size: 11.5px;">
                            Enter the domain without <code>http://</code> or <code>www</code>. Both root and <code>www.</code> traffic will be supported.
                        </small>
                    </div>
                    <div class="p-3 rounded-3 bg-light border" style="font-size: 12px;">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-info-circle me-1"></i> Next Step After Adding:</div>
                        <div class="text-muted">
                            1. Point your domain's <strong>A Record</strong> to <code>{{ $serverIp }}</code> (or CNAME to <code>{{ $cnameTarget }}</code>).<br>
                            2. Add the generated <strong>TXT Record</strong> and click <strong>Verify DNS</strong>.<br>
                            3. Click <strong>Make Primary</strong> to automatically redirect your free subdomain to your custom domain.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-semibold" style="background: #EC134A; border: none;">
                        <i class="bi bi-link-45deg me-1"></i> Add Custom Domain
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
