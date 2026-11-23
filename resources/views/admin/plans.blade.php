@extends('admin.layouts.master')

@section('title', 'SaaS Plans & Packages - SiteBuilder')

@section('admin_content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0F172A;">
                @if($user->isSuperAdmin())
                    SaaS Plans &amp; Packages Manager
                @elseif($user->isReseller())
                    Reseller Partner Packages
                @else
                    Subscription Plans &amp; Upgrade
                @endif
            </h4>
            <p class="text-muted small mb-0">
                @if($user->isSuperAdmin())
                    Configure Client and Reseller packages, website limits, storage quotas, custom domain rules, and referral commission rates.
                @else
                    Choose or upgrade your package. Payments are processed securely via SSLCommerz (bKash, Nagad, Cards) or Manual Transfer.
                @endif
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if($user->isSuperAdmin())
                <div class="btn-group btn-group-sm me-2">
                    <a href="{{ route('admin.plans.index') }}" class="btn {{ !$audienceFilter ? 'btn-dark' : 'btn-outline-dark' }}">All Plans</a>
                    <a href="{{ route('admin.plans.index', ['audience' => 'client']) }}" class="btn {{ $audienceFilter === 'client' ? 'btn-dark' : 'btn-outline-dark' }}">Client Plans</a>
                    <a href="{{ route('admin.plans.index', ['audience' => 'reseller']) }}" class="btn {{ $audienceFilter === 'reseller' ? 'btn-dark' : 'btn-outline-dark' }}">Reseller Plans</a>
                </div>
                <button type="button" class="btn btn-danger fw-semibold px-3 py-2" data-bs-toggle="modal" data-bs-target="#createPlanModal" style="background: #EC134A; border: none; border-radius: 10px; font-size: 13px;">
                    <i class="bi bi-plus-lg me-1"></i> Create New Plan
                </button>
            @endif
        </div>
    </div>

    @if($activeSub && $activeSub->plan)
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: #0F172A; color: #FFFFFF;">
            <div class="card-body p-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <span class="badge bg-success me-2">{{ strtoupper($activeSub->status) }}</span>
                    <span class="fw-bold">{{ $activeSub->plan->name }}</span>
                    <span class="text-white-50 ms-2 small">
                        • Limit: {{ $activeSub->plan->site_limit }} Website(s) • Storage: {{ $activeSub->plan->storage_mb }} MB
                        @if($activeSub->ends_at)
                            • Valid Until: {{ $activeSub->ends_at->format('M d, Y') }}
                        @endif
                    </span>
                </div>
                <a href="{{ route('admin.billing.index') }}" class="btn btn-sm btn-warning fw-bold">
                    <i class="bi bi-receipt me-1"></i> View Billing &amp; Invoices
                </a>
            </div>
        </div>
    @endif

    <!-- Plans Grid -->
    <div class="row g-4">
        @forelse($plans as $plan)
            @php
                $isCurrentPlan = $activeSub && (int) $activeSub->plan_id === (int) $plan->id;
            @endphp
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm position-relative" style="border-radius: 16px; {{ $plan->is_featured ? 'border: 2px solid #EC134A !important;' : '' }}">
                    @if($plan->is_featured)
                        <span class="badge position-absolute top-0 end-0 m-3 px-3 py-1 rounded-pill" style="background: #EC134A; font-size: 10.5px;">MOST POPULAR</span>
                    @endif
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="mb-2">
                                <span class="badge {{ $plan->audience === 'reseller' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success' }} text-uppercase" style="font-size: 10px;">
                                    {{ $plan->audience }} PACKAGE
                                </span>
                                @if($plan->trial_days > 0)
                                    <span class="badge bg-info-subtle text-info ms-1" style="font-size: 10px;">{{ $plan->trial_days }}-DAY FREE TRIAL</span>
                                @endif
                            </div>

                            <h5 class="fw-bold text-dark mb-1">{{ $plan->name }}</h5>
                            <p class="text-muted small mb-3">{{ $plan->description }}</p>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex align-items-baseline gap-1">
                                    <span class="fs-3 fw-bolder text-dark">{{ $plan->currency }} {{ number_format($plan->price_monthly, 0) }}</span>
                                    <span class="text-muted small">/ month</span>
                                </div>
                                <div class="text-muted" style="font-size: 11.5px;">
                                    or <strong>{{ $plan->currency }} {{ number_format($plan->price_yearly, 0) }}</strong> / year (Save 2 months)
                                </div>
                            </div>

                            <ul class="list-unstyled small mb-4" style="line-height: 2;">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i><strong>{{ $plan->site_limit }}</strong> Website(s) with Isolated DB</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i><strong>{{ number_format($plan->storage_mb) }} MB</strong> SSD Media Storage</li>
                                <li>
                                    <i class="bi {{ $plan->custom_domain ? 'bi-check-circle-fill text-success' : 'bi-dash-circle text-muted' }} me-2"></i>
                                    {{ $plan->custom_domain ? 'Custom Domain + Caddy Auto-SSL' : 'Free Subdomain Only (.yourhub.com)' }}
                                </li>
                                @if($plan->audience === 'reseller')
                                    <li><i class="bi bi-check-circle-fill text-primary me-2"></i><strong>{{ $plan->commission_rate }}%</strong> Referral Commission (Mode B)</li>
                                    <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Extra Site Rate: <strong>{{ $plan->currency }} {{ number_format($plan->extra_site_price, 0) }}</strong></li>
                                @endif
                                @foreach($plan->features ?? [] as $feat)
                                    <li><i class="bi bi-check2 text-danger me-2"></i>{{ $feat }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div>
                            <form action="{{ route('admin.plans.subscribe', $plan->id) }}" method="POST" class="d-grid gap-2">
                                @csrf
                                <div class="d-flex gap-2 mb-1">
                                    <select name="billing_cycle" class="form-select form-select-sm" style="font-size: 12px;">
                                        <option value="monthly">Monthly ({{ $plan->currency }} {{ number_format($plan->price_monthly, 0) }})</option>
                                        <option value="yearly">Yearly ({{ $plan->currency }} {{ number_format($plan->price_yearly, 0) }})</option>
                                    </select>
                                    @if($accessibleTenants->isNotEmpty())
                                        <select name="tenant_id" class="form-select form-select-sm" style="font-size: 12px;">
                                            @foreach($accessibleTenants as $t)
                                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-sm fw-bold py-2 {{ $isCurrentPlan ? 'btn-dark' : 'btn-danger' }}" style="{{ !$isCurrentPlan ? 'background:#EC134A; border:none;' : '' }} border-radius: 9px;">
                                    <i class="bi bi-credit-card me-1"></i>
                                    {{ $isCurrentPlan ? 'Renew / Generate Invoice' : 'Subscribe & Pay' }}
                                </button>
                            </form>

                            @if($user->isSuperAdmin())
                                <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST" class="mt-2 text-end" onsubmit="return confirm('Delete plan {{ $plan->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 small text-decoration-none" style="font-size: 11.5px;">
                                        <i class="bi bi-trash me-1"></i>Delete Plan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">No plans configured yet.</div>
        @endforelse
    </div>

    @if($user->isSuperAdmin())
        <!-- Modal: Create New Plan -->
        <div class="modal fade" id="createPlanModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form action="{{ route('admin.plans.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Create New SaaS Plan / Package</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Target Audience</label>
                                <select name="audience" class="form-select" required>
                                    <option value="client">Client (Direct Website Owner)</option>
                                    <option value="reseller">Reseller Partner Package</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Plan Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Business Pro or Reseller Agency" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Short Description</label>
                                <input type="text" name="description" class="form-control" placeholder="Brief summary shown on pricing card...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Monthly Price (BDT)</label>
                                <input type="number" step="0.01" name="price_monthly" class="form-control" value="990" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Yearly Price (BDT)</label>
                                <input type="number" step="0.01" name="price_yearly" class="form-control" value="9900" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Free Trial Days</label>
                                <input type="number" name="trial_days" class="form-control" value="14">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Website Slot Limit (Tenants)</label>
                                <input type="number" name="site_limit" class="form-control" value="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Storage Quota (MB)</label>
                                <input type="number" name="storage_mb" class="form-control" value="1024" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Reseller Commission % (Mode B)</label>
                                <input type="number" step="0.1" name="commission_rate" class="form-control" value="15">
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="custom_domain" value="1" id="chkCustomDomain" checked>
                                    <label class="form-check-label small fw-semibold" for="chkCustomDomain">
                                        Allow Custom Domain Connection + Auto-SSL
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="chkFeatured">
                                    <label class="form-check-label small fw-semibold" for="chkFeatured">
                                        Highlight as "Most Popular" Featured Plan
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Bullet Features (One per line)</label>
                                <textarea name="features_text" rows="3" class="form-control no-rte" placeholder="All 11+ Industry Themes Unlocked&#10;Dedicated Isolated MySQL Database&#10;Priority Support"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger fw-semibold" style="background: #EC134A; border: none;">Save Plan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
