@extends('admin.layouts.master')

@section('title', 'Reseller Hub, Referral Link, Wallet & Payouts - SiteBuilder')

@section('admin_content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0F172A;">
                @if($user->isSuperAdmin())
                    Reseller Program Hub, Commissions &amp; Payout Requests
                @else
                    Reseller Partner Hub, Referral Link &amp; Wallet
                @endif
            </h4>
            <p class="text-muted small mb-0">
                Resellers operate under the <strong>{{ $globalSiteName ?? 'YourHub' }}</strong> brand via direct client creation or 30-day referral link tracking (<code>?ref=CODE</code>).
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if($resellerProfile)
                <button type="button" class="btn btn-outline-dark fw-semibold px-3 py-2" data-bs-toggle="modal" data-bs-target="#topUpWalletModal" style="border-radius: 10px; font-size: 13px;">
                    <i class="bi bi-plus-circle me-1"></i> Top-Up Wallet
                </button>
                <button type="button" class="btn btn-danger fw-semibold px-3 py-2" data-bs-toggle="modal" data-bs-target="#payoutRequestModal" style="background: #EC134A; border: none; border-radius: 10px; font-size: 13px;">
                    <i class="bi bi-cash-coin me-1"></i> Request Payout
                </button>
            @endif
        </div>
    </div>

    <!-- Reseller Profile, Referral Link & Mode A / Mode B Switcher -->
    @if($resellerProfile)
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 14px;">
                    <div class="text-muted small fw-bold text-uppercase">Wallet Balance</div>
                    <div class="fs-3 fw-bolder text-success my-1">৳{{ number_format($resellerProfile->wallet_balance, 2) }}</div>
                    <div class="text-muted" style="font-size: 11.5px;">Used for extra sites, renewals &amp; payouts</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 14px;">
                    <div class="text-muted small fw-bold text-uppercase">Site Quota (Mode A)</div>
                    <div class="fs-3 fw-bolder text-dark my-1">{{ $resellerProfile->sites_used }} / {{ $resellerProfile->site_quota }}</div>
                    <div class="text-muted" style="font-size: 11.5px;">Package slots used</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 14px;">
                    <div class="text-muted small fw-bold text-uppercase">Commission Rate (Mode B)</div>
                    <div class="fs-3 fw-bolder text-primary my-1">{{ $resellerProfile->commission_rate }}%</div>
                    <div class="text-muted" style="font-size: 11.5px;">Clicks: {{ $resellerProfile->referral_clicks }} • 30-day cookie</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 14px;">
                    <div class="text-muted small fw-bold text-uppercase">KYC Verification</div>
                    <div class="my-2">
                        @if($resellerProfile->kyc_status === 'approved')
                            <span class="badge bg-success px-3 py-2"><i class="bi bi-patch-check-fill me-1"></i> APPROVED</span>
                        @elseif($resellerProfile->kyc_status === 'rejected')
                            <span class="badge bg-danger px-3 py-2">REJECTED</span>
                        @else
                            <span class="badge bg-warning text-dark px-3 py-2">PENDING REVIEW</span>
                        @endif
                    </div>
                    <div class="text-muted" style="font-size: 11.5px;">{{ $resellerProfile->kyc_document_type }}: {{ $resellerProfile->kyc_document_number ?: 'Not submitted' }}</div>
                </div>
            </div>
        </div>

        <!-- Referral Link & Billing Mode Configuration Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
            <div class="card-body p-4">
                <div class="row g-4 align-items-center">
                    <div class="col-md-5 border-end">
                        <h6 class="fw-bold mb-1"><i class="bi bi-share-fill text-danger me-1"></i> Your Official Referral Link</h6>
                        <p class="text-muted small mb-2">
                            Share this link with clients. Any client who visits and signs up within 30 days is permanently linked under your reseller account.
                        </p>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control fw-semibold bg-light" value="{{ $resellerProfile->referralUrl() }}" readonly>
                            <span class="input-group-text bg-dark text-white fw-bold">{{ $resellerProfile->referral_code }}</span>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <form action="{{ route('admin.reseller.profile.update') }}" method="POST" class="row g-2 align-items-end">
                            @csrf
                            <input type="hidden" name="reseller_id" value="{{ $resellerProfile->id }}">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold mb-1">Agency / Reseller Name</label>
                                <input type="text" name="company_name" value="{{ $resellerProfile->company_name }}" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold mb-1">Billing Mode (Section 6.2)</label>
                                <select name="billing_mode" class="form-select form-select-sm">
                                    <option value="reseller_collects" {{ $resellerProfile->billing_mode === 'reseller_collects' ? 'selected' : '' }}>
                                        Mode A: Reseller Collects
                                    </option>
                                    <option value="commission" {{ $resellerProfile->billing_mode === 'commission' ? 'selected' : '' }}>
                                        Mode B: Referral Commission ({{ $resellerProfile->commission_rate }}%)
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold mb-1">KYC Doc ({{ $resellerProfile->kyc_document_type }})</label>
                                <input type="hidden" name="kyc_document_type" value="{{ $resellerProfile->kyc_document_type }}">
                                <input type="text" name="kyc_document_number" value="{{ $resellerProfile->kyc_document_number }}" class="form-control form-control-sm" placeholder="NID / Trade #">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-dark w-100 fw-semibold">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Super Admin: All Resellers Directory & KYC Approval -->
    @if($user->isSuperAdmin())
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
            <div class="card-header bg-white border-0 pt-3 pb-2 px-4">
                <h6 class="fw-bold mb-0">All Reseller Partners &amp; KYC Verification</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0" style="font-size: 12.5px;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Company &amp; Owner</th>
                            <th>Referral Code</th>
                            <th>Billing Mode</th>
                            <th>Site Quota</th>
                            <th>Wallet Balance</th>
                            <th>KYC Status</th>
                            <th class="text-end pe-4">Update KYC / Quota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allResellers as $res)
                            @if($res)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $res->company_name }}</div>
                                        <div class="text-muted" style="font-size: 11px;">{{ $res->user?->email }}</div>
                                    </td>
                                    <td><code>{{ $res->referral_code }}</code> ({{ $res->referral_clicks }} clicks)</td>
                                    <td>
                                        <span class="badge {{ $res->billing_mode === 'commission' ? 'bg-primary-subtle text-primary' : 'bg-dark-subtle text-dark' }}">
                                            {{ $res->billing_mode === 'commission' ? 'Mode B: Commission (' . $res->commission_rate . '%)' : 'Mode A: Reseller Collects' }}
                                        </span>
                                    </td>
                                    <td>{{ $res->sites_used }} / {{ $res->site_quota }}</td>
                                    <td class="fw-bold text-success">৳{{ number_format($res->wallet_balance, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $res->kyc_status === 'approved' ? 'bg-success-subtle text-success' : ($res->kyc_status === 'rejected' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') }}">
                                            {{ strtoupper($res->kyc_status) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('admin.reseller.kyc.update', $res->id) }}" method="POST" class="d-inline-flex align-items-center gap-1">
                                            @csrf
                                            <select name="kyc_status" class="form-select form-select-sm" style="width: 110px; font-size: 11.5px;">
                                                <option value="approved" {{ $res->kyc_status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="pending" {{ $res->kyc_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="rejected" {{ $res->kyc_status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                            <input type="number" name="site_quota" value="{{ $res->site_quota }}" class="form-control form-control-sm" style="width: 70px; font-size: 11.5px;" title="Site Quota">
                                            <button type="submit" class="btn btn-sm btn-dark" style="font-size: 11.5px;">Apply</button>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-3 text-muted">No resellers registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Commissions Table (Mode B) -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header bg-white border-0 pt-3 pb-2 px-4">
                    <h6 class="fw-bold mb-0">Referral Commissions (Mode B)</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size: 12.5px;">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Client / Invoice</th>
                                <th>Commission</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commissions as $cm)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">{{ $cm->client?->name }}</div>
                                        <div class="text-muted" style="font-size: 11px;">{{ $cm->invoice?->invoice_number }} (৳{{ number_format($cm->invoice_amount, 0) }})</div>
                                    </td>
                                    <td class="fw-bold text-primary">৳{{ number_format($cm->amount, 2) }} ({{ $cm->rate }}%)</td>
                                    <td>
                                        @if($cm->status === 'approved')
                                            <span class="badge bg-success-subtle text-success">Credited</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        @if($user->isSuperAdmin() && $cm->status === 'pending')
                                            <form action="{{ route('admin.reseller.commissions.approve', $cm->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success py-0 px-2" style="font-size: 11px;">Release Now</button>
                                            </form>
                                        @else
                                            <span class="text-muted" style="font-size: 11px;">{{ $cm->available_at?->format('M d') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No commissions recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payout Requests Table -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header bg-white border-0 pt-3 pb-2 px-4">
                    <h6 class="fw-bold mb-0">Wallet Payout Requests</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size: 12.5px;">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Method &amp; Account</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payoutRequests as $pr)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">{{ $pr->method }} ({{ $pr->account_info }})</div>
                                        <div class="text-muted" style="font-size: 11px;">{{ $pr->reseller?->company_name }}</div>
                                    </td>
                                    <td class="fw-bold">৳{{ number_format($pr->amount, 2) }}</td>
                                    <td>
                                        @if($pr->status === 'paid')
                                            <span class="badge bg-success-subtle text-success">Paid</span>
                                        @elseif($pr->status === 'rejected')
                                            <span class="badge bg-danger-subtle text-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        @if($user->isSuperAdmin() && $pr->status === 'pending')
                                            <form action="{{ route('admin.reseller.payouts.process', $pr->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="paid">
                                                <button type="submit" class="btn btn-sm btn-success py-0 px-2" style="font-size: 11px;">Mark Paid</button>
                                            </form>
                                            <form action="{{ route('admin.reseller.payouts.process', $pr->id) }}" method="POST" class="d-inline ms-1">
                                                @csrf
                                                <input type="hidden" name="action" value="rejected">
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 11px;">Reject</button>
                                            </form>
                                        @else
                                            <span class="text-muted" style="font-size: 11px;">{{ $pr->transaction_reference ?: '-' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No payout requests submitted yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Wallet Transactions Ledger -->
    <div class="card border-0 shadow-sm mt-4" style="border-radius: 14px;">
        <div class="card-header bg-white border-0 pt-3 pb-2 px-4">
            <h6 class="fw-bold mb-0">Wallet Transaction History</h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 12.5px;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Reference</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th class="text-end pe-4">Balance After</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($walletTransactions as $wt)
                        <tr>
                            <td class="ps-4 text-muted">{{ $wt->created_at?->format('M d, Y H:i') }}</td>
                            <td><code>{{ $wt->reference }}</code></td>
                            <td>{{ $wt->description }}</td>
                            <td>
                                <span class="badge {{ $wt->type === 'credit' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} text-uppercase">
                                    {{ $wt->type }}
                                </span>
                            </td>
                            <td class="fw-bold {{ $wt->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $wt->type === 'credit' ? '+' : '-' }}৳{{ number_format($wt->amount, 2) }}
                            </td>
                            <td class="text-end pe-4 fw-bold">৳{{ number_format($wt->balance_after, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No wallet transactions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($resellerProfile)
        <!-- Modal: Top-Up Wallet -->
        <div class="modal fade" id="topUpWalletModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('admin.reseller.wallet.topup') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                    @csrf
                    <input type="hidden" name="reseller_id" value="{{ $resellerProfile->id }}">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Top-Up Reseller Wallet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Top-Up Amount (BDT)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" value="2000" min="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Payment Method</label>
                            <select name="method" class="form-select">
                                <option value="SSLCommerz">SSLCommerz Instant Top-Up</option>
                                <option value="bKash Transfer">bKash Transfer</option>
                                <option value="Bank Deposit">Bank Deposit</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Transaction Reference (Optional)</label>
                            <input type="text" name="reference" class="form-control" placeholder="e.g. TRX998877">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-semibold">Add Funds to Wallet</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Request Payout -->
        <div class="modal fade" id="payoutRequestModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('admin.reseller.payouts.request') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Request Wallet Payout</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="p-3 rounded-3 bg-light border mb-3 small">
                            Available Wallet Balance: <strong class="text-success">৳{{ number_format($resellerProfile->wallet_balance, 2) }}</strong> (Minimum withdrawal: ৳500)
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Withdrawal Amount (BDT)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" min="500" value="1000" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Payout Method</label>
                            <select name="method" class="form-select" required>
                                <option value="bKash">bKash Personal / Agent</option>
                                <option value="Nagad">Nagad</option>
                                <option value="Rocket">Rocket</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Account / Mobile Number &amp; Details</label>
                            <input type="text" name="account_info" class="form-control" placeholder="e.g. 017XXXXXXXX (bKash Personal)" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger fw-semibold" style="background: #EC134A; border: none;">Submit Payout Request</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
