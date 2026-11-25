@extends('admin.layouts.master')

@section('title', 'SaaS Control Center - SiteBuilder')

@push('module_styles')
<style>
    .cc-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
    .cc-header h2 { font-size: 26px; font-weight: 800; color: #0F172A; letter-spacing: -0.02em; margin: 0 0 4px 0; }
    .cc-header p { font-size: 14px; color: #64748B; margin: 0; }
    .cc-updated { font-size: 12.5px; color: #64748B; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 10px; padding: 8px 14px; }

    .cc-kpi-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; margin-bottom: 24px; }
    .cc-kpi { background: #FFFFFF; border: 1px solid #EEF2F6; border-radius: 16px; padding: 20px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02); }
    .cc-kpi-top { display: flex; align-items: center; gap: 14px; }
    .cc-kpi-icon { width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
    .cc-kpi span.lbl { display: block; font-size: 12.5px; font-weight: 500; color: #64748B; margin-bottom: 2px; }
    .cc-kpi h3 { font-size: 24px; font-weight: 800; color: #0F172A; margin: 0; line-height: 1.15; }
    .cc-kpi .sub { font-size: 12px; color: #94A3B8; margin: 12px 0 0 0; }

    .cc-grid-2 { display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; margin-bottom: 24px; }
    .cc-grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; margin-bottom: 24px; }

    .cc-panel { background: #FFFFFF; border: 1px solid #EEF2F6; border-radius: 16px; padding: 20px 22px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02); min-width: 0; }
    .cc-panel-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 14px; }
    .cc-panel-head h4 { font-size: 16px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px; }
    .cc-panel-head a { font-size: 12.5px; font-weight: 600; color: #EC134A; text-decoration: none; white-space: nowrap; }
    .cc-count { background: #FFE4EC; color: #EC134A; font-size: 11px; font-weight: 700; border-radius: 999px; padding: 2px 8px; }
    .cc-count.zero { background: #F1F5F9; color: #94A3B8; }

    .cc-bars { display: flex; align-items: flex-end; gap: 14px; height: 170px; padding-top: 10px; }
    .cc-bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; gap: 6px; }
    .cc-bar { width: 100%; max-width: 46px; background: linear-gradient(180deg, #EC134A 0%, #FF85A1 100%); border-radius: 8px 8px 4px 4px; min-height: 4px; }
    .cc-bar-val { font-size: 11px; font-weight: 700; color: #334155; }
    .cc-bar-lbl { font-size: 11.5px; color: #64748B; font-weight: 600; }

    .cc-status-row { display: flex; align-items: center; justify-content: space-between; padding: 9px 0; border-bottom: 1px solid #F1F5F9; font-size: 13.5px; color: #334155; }
    .cc-status-row:last-child { border-bottom: 0; }
    .cc-dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; margin-right: 8px; }

    .cc-list { list-style: none; padding: 0; margin: 0; }
    .cc-list li { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 10px 0; border-bottom: 1px solid #F1F5F9; font-size: 13px; }
    .cc-list li:last-child { border-bottom: 0; }
    .cc-list .main { min-width: 0; }
    .cc-list .main strong { display: block; color: #0F172A; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cc-list .main small { color: #64748B; }
    .cc-empty { font-size: 13px; color: #94A3B8; padding: 14px 0; text-align: center; }
    .cc-actions { display: flex; gap: 6px; flex-shrink: 0; }
    .cc-actions .btn { font-size: 11.5px; padding: 3px 9px; }

    .cc-quick { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .cc-quick a { display: flex; align-items: center; gap: 10px; padding: 13px 14px; border: 1px solid #EEF2F6; border-radius: 12px; text-decoration: none; color: #0F172A; font-size: 13px; font-weight: 600; background: #FCFDFE; transition: all 0.15s; }
    .cc-quick a:hover { border-color: #FFC2D2; background: #FFF5F8; color: #EC134A; }
    .cc-quick i { font-size: 17px; color: #EC134A; }

    .cc-health-ok { color: #059669; font-weight: 600; font-size: 13.5px; }
    .cc-health li { font-size: 12.5px; color: #B45309; padding: 5px 0; }

    @media (max-width: 1200px) {
        .cc-kpi-grid, .cc-quick { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .cc-grid-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .cc-grid-2 { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .cc-kpi-grid, .cc-quick, .cc-grid-3 { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('admin_content')
    @php
        $money = fn ($v) => $currency.' '.number_format((float) $v, 0);
        $maxRevenue = max(1, $revenueByMonth->max('amount'));
    @endphp

    <div class="cc-header">
        <div>
            <h2>SaaS Control Center</h2>
            <p>Revenue, tenants and everything waiting for your decision, in one place.</p>
        </div>
        <span class="cc-updated"><i class="bi bi-clock-history me-1"></i> Updated {{ $generatedAt->format('d M Y, h:i A') }}</span>
    </div>

    {{-- KPIs --}}
    <div class="cc-kpi-grid">
        <div class="cc-kpi">
            <div class="cc-kpi-top">
                <div class="cc-kpi-icon" style="background:#FFE4EC;color:#EC134A;"><i class="bi bi-graph-up-arrow"></i></div>
                <div><span class="lbl">Monthly Recurring Revenue</span><h3>{{ $money($stats['mrr']) }}</h3></div>
            </div>
            <p class="sub">From active subscriptions (yearly ÷ 12)</p>
        </div>
        <div class="cc-kpi">
            <div class="cc-kpi-top">
                <div class="cc-kpi-icon" style="background:#DCFCE7;color:#10B981;"><i class="bi bi-cash-coin"></i></div>
                <div><span class="lbl">Collected This Month</span><h3>{{ $money($stats['revenue_month']) }}</h3></div>
            </div>
            <p class="sub">All time: {{ $money($stats['revenue_total']) }}</p>
        </div>
        <div class="cc-kpi">
            <div class="cc-kpi-top">
                <div class="cc-kpi-icon" style="background:#DBEAFE;color:#3B82F6;"><i class="bi bi-hdd-network-fill"></i></div>
                <div><span class="lbl">Tenant Websites</span><h3>{{ number_format($stats['tenants_total']) }}</h3></div>
            </div>
            <p class="sub">{{ $stats['tenants_active'] }} active · {{ $stats['tenants_trial'] }} trial · {{ $stats['tenants_suspended'] }} suspended</p>
        </div>
        <div class="cc-kpi">
            <div class="cc-kpi-top">
                <div class="cc-kpi-icon" style="background:#FEF3C7;color:#D97706;"><i class="bi bi-receipt"></i></div>
                <div><span class="lbl">Unpaid Invoices</span><h3>{{ number_format($stats['unpaid_invoices']) }}</h3></div>
            </div>
            <p class="sub">{{ $money($stats['unpaid_amount']) }} outstanding · {{ $stats['past_due'] }} past due</p>
        </div>
    </div>

    {{-- Revenue chart + platform status --}}
    <div class="cc-grid-2">
        <div class="cc-panel">
            <div class="cc-panel-head">
                <h4><i class="bi bi-bar-chart-fill" style="color:#EC134A;"></i> Revenue, last 6 months</h4>
                <a href="{{ route('admin.billing.index') }}">Billing <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="cc-bars">
                @foreach($revenueByMonth as $m)
                    <div class="cc-bar-col">
                        <span class="cc-bar-val">{{ $m['amount'] > 0 ? number_format($m['amount'] / 1000, 1).'k' : '0' }}</span>
                        <div class="cc-bar" style="height: {{ max(2, round($m['amount'] / $maxRevenue * 100)) }}%;"></div>
                        <span class="cc-bar-lbl">{{ $m['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="cc-panel">
            <div class="cc-panel-head">
                <h4><i class="bi bi-activity" style="color:#3B82F6;"></i> Platform</h4>
                <a href="{{ route('admin.saas.accounts') }}">Accounts <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="cc-status-row"><span><span class="cc-dot" style="background:#10B981;"></span>Active tenants</span><strong>{{ $stats['tenants_active'] }}</strong></div>
            <div class="cc-status-row"><span><span class="cc-dot" style="background:#3B82F6;"></span>On trial</span><strong>{{ $stats['tenants_trial'] }}</strong></div>
            <div class="cc-status-row"><span><span class="cc-dot" style="background:#F59E0B;"></span>Waiting email verification</span><strong>{{ $stats['tenants_pending'] }}</strong></div>
            <div class="cc-status-row"><span><span class="cc-dot" style="background:#EF4444;"></span>Suspended / expired</span><strong>{{ $stats['tenants_suspended'] }}</strong></div>
            <div class="cc-status-row"><span><span class="cc-dot" style="background:#8B5CF6;"></span>Clients</span><strong>{{ $stats['clients'] }}</strong></div>
            <div class="cc-status-row"><span><span class="cc-dot" style="background:#EC134A;"></span>Resellers</span><strong>{{ $stats['resellers'] }}</strong></div>
        </div>
    </div>

    {{-- Action queues --}}
    <div class="cc-grid-3">
        <div class="cc-panel">
            <div class="cc-panel-head">
                <h4>Payments to approve <span class="cc-count {{ $queueCounts['payments'] ? '' : 'zero' }}">{{ $queueCounts['payments'] }}</span></h4>
                <a href="{{ route('admin.billing.index') }}">All</a>
            </div>
            <ul class="cc-list">
                @forelse($queues['payments'] as $p)
                    <li>
                        <div class="main">
                            <strong>{{ $p->user?->name ?? 'User #'.$p->user_id }} · {{ $p->currency }} {{ number_format($p->amount, 0) }}</strong>
                            <small>{{ $p->method }} {{ $p->sender_number }} · TRX {{ $p->gateway_txn_id }}</small>
                        </div>
                        <div class="cc-actions">
                            <form action="{{ route('admin.billing.payments.approve', $p->id) }}" method="POST">@csrf<button class="btn btn-success" title="Approve and activate">Approve</button></form>
                            <form action="{{ route('admin.billing.payments.reject', $p->id) }}" method="POST" onsubmit="return confirm('Reject this payment?');">@csrf<button class="btn btn-outline-danger">Reject</button></form>
                        </div>
                    </li>
                @empty
                    <li class="cc-empty">No manual payments waiting.</li>
                @endforelse
            </ul>
        </div>

        <div class="cc-panel">
            <div class="cc-panel-head">
                <h4>Open support tickets <span class="cc-count {{ $queueCounts['tickets'] ? '' : 'zero' }}">{{ $queueCounts['tickets'] }}</span></h4>
                <a href="{{ route('admin.support.tickets') }}">All</a>
            </div>
            <ul class="cc-list">
                @forelse($queues['tickets'] as $t)
                    <li>
                        <div class="main">
                            <strong>{{ $t->subject }}</strong>
                            <small>{{ $t->ticket_number }} · {{ $t->user?->name }} · {{ $t->created_at?->diffForHumans() }}</small>
                        </div>
                        <span class="badge {{ in_array($t->priority, ['urgent', 'high']) ? 'bg-danger' : 'bg-secondary' }}">{{ ucfirst($t->priority) }}</span>
                    </li>
                @empty
                    <li class="cc-empty">No open tickets.</li>
                @endforelse
            </ul>
        </div>

        <div class="cc-panel">
            <div class="cc-panel-head">
                <h4>Expiring in 7 days <span class="cc-count {{ $queueCounts['expiring'] ? '' : 'zero' }}">{{ $queueCounts['expiring'] }}</span></h4>
                <a href="{{ route('admin.billing.index') }}">All</a>
            </div>
            <ul class="cc-list">
                @forelse($queues['expiring'] as $s)
                    <li>
                        <div class="main">
                            <strong>{{ $s->tenant?->name ?? $s->user?->name }}</strong>
                            <small>{{ $s->plan?->name }} · {{ ucfirst($s->status) }}</small>
                        </div>
                        <small class="text-danger fw-semibold">{{ $s->ends_at?->format('d M') }}</small>
                    </li>
                @empty
                    <li class="cc-empty">Nothing expiring this week.</li>
                @endforelse
            </ul>
        </div>

        <div class="cc-panel">
            <div class="cc-panel-head">
                <h4>Custom domains to verify <span class="cc-count {{ $queueCounts['domains'] ? '' : 'zero' }}">{{ $queueCounts['domains'] }}</span></h4>
                <a href="{{ route('admin.domains.index') }}">All</a>
            </div>
            <ul class="cc-list">
                @forelse($queues['domains'] as $d)
                    <li>
                        <div class="main">
                            <strong>{{ $d->domain }}</strong>
                            <small>{{ $d->tenant?->name }}</small>
                        </div>
                        <form action="{{ route('admin.domains.verify', $d->id) }}" method="POST" class="cc-actions">@csrf<button class="btn btn-outline-primary">Check DNS</button></form>
                    </li>
                @empty
                    <li class="cc-empty">No domains waiting.</li>
                @endforelse
            </ul>
        </div>

        <div class="cc-panel">
            <div class="cc-panel-head">
                <h4>Reseller payouts <span class="cc-count {{ $queueCounts['payouts'] ? '' : 'zero' }}">{{ $queueCounts['payouts'] }}</span></h4>
                <a href="{{ route('admin.reseller.hub') }}">Reseller hub</a>
            </div>
            <ul class="cc-list">
                @forelse($queues['payouts'] as $po)
                    <li>
                        <div class="main">
                            <strong>{{ $po->reseller?->user?->name ?? $po->reseller?->company_name }} · {{ $currency }} {{ number_format($po->amount, 0) }}</strong>
                            <small>{{ $po->method }} · {{ $po->created_at?->diffForHumans() }}</small>
                        </div>
                        <a href="{{ route('admin.reseller.hub') }}" class="btn btn-sm btn-outline-secondary" style="font-size:11.5px;">Process</a>
                    </li>
                @empty
                    <li class="cc-empty">No payout requests.</li>
                @endforelse
            </ul>
        </div>

        <div class="cc-panel">
            <div class="cc-panel-head">
                <h4>Reseller KYC <span class="cc-count {{ $queueCounts['kyc'] ? '' : 'zero' }}">{{ $queueCounts['kyc'] }}</span></h4>
                <a href="{{ route('admin.reseller.hub') }}">Reseller hub</a>
            </div>
            <ul class="cc-list">
                @forelse($queues['kyc'] as $r)
                    <li>
                        <div class="main">
                            <strong>{{ $r->company_name ?: $r->user?->name }}</strong>
                            <small>{{ strtoupper((string) $r->kyc_document_type) }} {{ $r->kyc_document_number }}</small>
                        </div>
                        <div class="cc-actions">
                            <form action="{{ route('admin.reseller.kyc.update', $r->id) }}" method="POST">@csrf<input type="hidden" name="kyc_status" value="approved"><button class="btn btn-success">Approve</button></form>
                            <form action="{{ route('admin.reseller.kyc.update', $r->id) }}" method="POST">@csrf<input type="hidden" name="kyc_status" value="rejected"><button class="btn btn-outline-danger">Reject</button></form>
                        </div>
                    </li>
                @empty
                    <li class="cc-empty">No KYC waiting.</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Recent signups + health --}}
    <div class="cc-grid-2">
        <div class="cc-panel">
            <div class="cc-panel-head">
                <h4><i class="bi bi-person-plus-fill" style="color:#10B981;"></i> Latest tenant signups</h4>
                <a href="{{ route('admin.tenants.index') }}">All tenants <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0" style="font-size: 13px;">
                    <thead><tr class="text-muted"><th>Site</th><th>Owner</th><th>Theme</th><th>Plan</th><th>Status</th><th class="text-end">Joined</th></tr></thead>
                    <tbody>
                        @forelse($recentTenants as $t)
                            <tr>
                                <td class="fw-semibold">{{ $t->name }}<br><small class="text-muted">{{ $t->subdomain }}</small></td>
                                <td>{{ $t->owner?->name ?? '-' }}</td>
                                <td>{{ $t->theme_module }}</td>
                                <td>{{ $t->plan?->name ?? '-' }}</td>
                                <td>
                                    @php $badge = ['active' => 'bg-success', 'trial' => 'bg-primary', 'pending_verification' => 'bg-warning text-dark'][$t->status] ?? 'bg-danger'; @endphp
                                    <span class="badge {{ $badge }}">{{ str_replace('_', ' ', $t->status) }}</span>
                                </td>
                                <td class="text-end text-muted">{{ $t->created_at?->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="cc-empty">No tenants yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="cc-panel">
            <div class="cc-panel-head">
                <h4><i class="bi bi-shield-check" style="color:#D97706;"></i> Production health</h4>
                <a href="{{ route('admin.email.settings') }}">Email setup</a>
            </div>
            @if(empty($readinessIssues))
                <p class="cc-health-ok mb-0"><i class="bi bi-check-circle-fill me-1"></i> All production checks passed.</p>
            @else
                <ul class="cc-health ps-3 mb-2">
                    @foreach($readinessIssues as $issue)
                        <li>{{ $issue }}</li>
                    @endforeach
                </ul>
                <small class="text-muted">Same checks as <code>php artisan saas:doctor</code>.</small>
            @endif
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="cc-panel mb-4">
        <div class="cc-panel-head"><h4><i class="bi bi-lightning-charge-fill" style="color:#EC134A;"></i> Manage</h4></div>
        <div class="cc-quick">
            <a href="{{ route('admin.tenants.index') }}"><i class="bi bi-hdd-network-fill"></i> Tenants</a>
            <a href="{{ route('admin.saas.accounts') }}"><i class="bi bi-people-fill"></i> Resellers &amp; Clients</a>
            <a href="{{ route('admin.plans.index') }}"><i class="bi bi-box-seam-fill"></i> Plans &amp; Packages</a>
            <a href="{{ route('admin.billing.index') }}"><i class="bi bi-receipt-cutoff"></i> Subscriptions &amp; Invoices</a>
            <a href="{{ route('admin.domains.index') }}"><i class="bi bi-link-45deg"></i> Domains &amp; SSL</a>
            <a href="{{ route('admin.reseller.hub') }}"><i class="bi bi-wallet2"></i> Reseller Hub</a>
            <a href="{{ route('admin.backups.index') }}"><i class="bi bi-database-fill-check"></i> Backups</a>
            <a href="{{ route('admin.support.tickets') }}"><i class="bi bi-life-preserver"></i> Support Tickets</a>
            <a href="{{ route('admin.email.settings') }}"><i class="bi bi-envelope-paper-fill"></i> Email &amp; SMTP</a>
            <a href="{{ route('admin.websites') }}"><i class="bi bi-globe2"></i> Themes &amp; Modules</a>
            <a href="{{ route('admin.saas.homepage') }}"><i class="bi bi-layout-text-window-reverse"></i> SaaS Homepage</a>
            <a href="{{ route('admin.settings') }}"><i class="bi bi-gear-fill"></i> Settings</a>
        </div>
    </div>
@endsection
