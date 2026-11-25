@extends('admin.layouts.master')

@section('title', 'Subscriptions, Invoices & Payments - SiteBuilder')

@section('admin_content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0F172A;">Subscriptions, Invoices &amp; Payments</h4>
            <p class="text-muted small mb-0">
                Pay invoices online via <strong>SSLCommerz</strong> (Cards, bKash, Nagad, Rocket) or submit a <strong>Manual Payment Txn ID</strong>.
            </p>
        </div>
        <a href="{{ route('admin.plans.index') }}" class="btn btn-danger fw-semibold px-3 py-2" style="background: #EC134A; border: none; border-radius: 10px; font-size: 13px;">
            <i class="bi bi-box-seam me-1"></i> Browse Plans &amp; Upgrade
        </a>
    </div>

    <!-- Pending Manual Payments Approval Queue (Visible to Super Admin when pending payments exist) -->
    @if($user->isSuperAdmin() && $pendingPayments->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; border-left: 4px solid #F59E0B !important;">
            <div class="card-header bg-white border-0 pt-3 pb-2 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-hourglass-split text-warning me-1"></i> Pending Manual Payments Awaiting Approval ({{ $pendingPayments->count() }})
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0" style="font-size: 12.5px;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Invoice</th>
                            <th>Client / Reseller</th>
                            <th>Method &amp; Sender</th>
                            <th>Transaction ID</th>
                            <th>Amount</th>
                            <th class="text-end pe-4">Approve / Reject</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingPayments as $pp)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $pp->invoice?->invoice_number }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $pp->user?->name }}</div>
                                    <div class="text-muted" style="font-size: 11px;">{{ $pp->user?->email }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-warning-subtle text-dark">{{ $pp->method }}</span>
                                    <span class="ms-1 text-muted">{{ $pp->sender_number }}</span>
                                </td>
                                <td><code>{{ $pp->gateway_txn_id }}</code></td>
                                <td class="fw-bold">{{ $pp->currency }} {{ number_format($pp->amount, 2) }}</td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('admin.billing.payments.approve', $pp->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success fw-semibold" style="font-size: 11.5px;">
                                            <i class="bi bi-check-lg me-1"></i> Approve &amp; Activate
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.billing.payments.reject', $pp->id) }}" method="POST" class="d-inline ms-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size: 11.5px;">
                                            Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Invoices Table -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-header bg-white border-0 pt-3 pb-2 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="fw-bold mb-0">Invoices</h6>
            <div class="btn-group btn-group-sm">
                <a href="{{ route('admin.billing.index') }}" class="btn {{ !$statusFilter ? 'btn-dark' : 'btn-outline-secondary' }}">All</a>
                <a href="{{ route('admin.billing.index', ['status' => 'unpaid']) }}" class="btn {{ $statusFilter === 'unpaid' ? 'btn-dark' : 'btn-outline-secondary' }}">Unpaid</a>
                <a href="{{ route('admin.billing.index', ['status' => 'pending_approval']) }}" class="btn {{ $statusFilter === 'pending_approval' ? 'btn-dark' : 'btn-outline-secondary' }}">Pending Approval</a>
                <a href="{{ route('admin.billing.index', ['status' => 'paid']) }}" class="btn {{ $statusFilter === 'paid' ? 'btn-dark' : 'btn-outline-secondary' }}">Paid</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 13px;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Invoice #</th>
                        <th>Plan &amp; Website</th>
                        <th>Customer</th>
                        <th>Billing Cycle</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Payment / Invoice PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $inv->invoice_number }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $inv->plan?->name ?? 'SaaS Plan' }}</div>
                                @if($inv->tenant)
                                    <div class="text-muted" style="font-size: 11px;">Website: {{ $inv->tenant->name }} (<code>{{ $inv->tenant->db_name }}</code>)</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $inv->user?->name }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $inv->user?->email }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border text-uppercase">{{ $inv->billing_cycle }}</span></td>
                            <td class="fw-bold">{{ $inv->currency }} {{ number_format($inv->total, 2) }}</td>
                            <td>
                                @if($inv->status === 'paid')
                                    <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle-fill me-1"></i> Paid</span>
                                @elseif($inv->status === 'pending_approval')
                                    <span class="badge bg-warning-subtle text-warning"><i class="bi bi-hourglass-split me-1"></i> Pending Approval</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex align-items-center gap-1">
                                    @if($inv->status === 'unpaid')
                                        <form action="{{ route('admin.billing.pay-sslcommerz', $inv->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary fw-semibold" style="font-size: 11.5px; border-radius: 7px;">
                                                <i class="bi bi-credit-card me-1"></i> Pay Online (SSLCommerz)
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-dark fw-semibold"
                                                data-bs-toggle="modal"
                                                data-bs-target="#manualPayModal-{{ $inv->id }}"
                                                style="font-size: 11.5px; border-radius: 7px;">
                                            Manual bKash/Bank
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.billing.invoices.show', $inv->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" style="font-size: 11.5px; border-radius: 7px;">
                                        <i class="bi bi-printer me-1"></i> Invoice
                                    </a>
                                </div>

                                @if($inv->status === 'unpaid')
                                    <!-- Modal: Manual Payment Submission -->
                                    <div class="modal fade text-start" id="manualPayModal-{{ $inv->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form action="{{ route('admin.billing.pay-manual', $inv->id) }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                                                @csrf
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <h5 class="modal-title fw-bold">Submit Manual Payment ({{ $inv->invoice_number }})</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="p-3 rounded-3 bg-light border mb-3" style="font-size: 12px;">
                                                        Send <strong>{{ $inv->currency }} {{ number_format($inv->total, 2) }}</strong> to our Merchant bKash/Nagad: <strong>01812-XXXXXX</strong> and submit your Transaction ID below.
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Payment Method</label>
                                                        <select name="method" class="form-select" required>
                                                            <option value="bKash">bKash</option>
                                                            <option value="Nagad">Nagad</option>
                                                            <option value="Rocket">Rocket</option>
                                                            <option value="Bank Transfer">Bank Transfer</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Sender Mobile / Account Number</label>
                                                        <input type="text" name="sender_number" class="form-control" placeholder="e.g. 017XXXXXXXX" required>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Transaction ID (TrxID)</label>
                                                        <input type="text" name="gateway_txn_id" class="form-control" placeholder="e.g. BKS982347XYZ" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger fw-semibold" style="background: #EC134A; border: none;">Submit for Verification</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No invoices generated yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
@endsection
