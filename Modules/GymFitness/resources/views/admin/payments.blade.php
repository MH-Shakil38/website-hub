@extends('admin.layouts.master')

@section('title', 'Payments & Revenue Management — FitPulse Gym Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Payments, Dues &amp; Revenue Reports</h3>
                <span class="sb-badge-pill sb-badge-green">gym_payments + core_transactions</span>
            </div>
            <p class="text-muted small mb-0">Track membership fees, registration fees, renewals, personal training payments, dues, and refunds synced with global accounting.</p>
        </div>

        <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
            <i class="bi bi-plus-lg me-1"></i> Record New Payment
        </button>
    </div>

    {{-- Revenue Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="bg-white border rounded-4 p-3 shadow-sm">
                <div class="text-muted small fw-semibold">Total Paid Revenue</div>
                <div class="fs-3 fw-bold text-success">${{ number_format($totalPaid, 2) }}</div>
                <span class="small text-muted">Synced with Global Payments</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-white border rounded-4 p-3 shadow-sm">
                <div class="text-muted small fw-semibold">Pending / Due Payments</div>
                <div class="fs-3 fw-bold text-warning">${{ number_format($totalDue, 2) }}</div>
                <span class="small text-muted">Requires Follow-up</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-white border rounded-4 p-3 shadow-sm">
                <div class="text-muted small fw-semibold">Total Refunds</div>
                <div class="fs-3 fw-bold text-danger">${{ number_format($totalRefunds, 2) }}</div>
                <span class="small text-muted">Processed Refunds</span>
            </div>
        </div>
    </div>

    {{-- Payments Table --}}
    <div class="sb-table-card">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold mb-0">Payment Invoices &amp; Ledger</h6>
            <form action="{{ route('admin.gymfitness.payments.index') }}" method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width:140px;">
                    <option value="">All Status</option>
                    @foreach(['Paid', 'Pending', 'Due', 'Refunded'] as $st)
                        <option value="{{ $st }}" {{ $statusFilter === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()" style="width:180px;">
                    <option value="">All Payment Types</option>
                    @foreach(['Membership Fee', 'Registration Fee', 'Renewal Payment', 'Personal Training', 'Additional Service'] as $tp)
                        <option value="{{ $tp }}" {{ $typeFilter === $tp ? 'selected' : '' }}>{{ $tp }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Invoice &amp; Date</th>
                        <th>Member</th>
                        <th>Payment Type</th>
                        <th>Total / Paid / Due</th>
                        <th>Method &amp; Ref</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $pay)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $pay->invoice_no }}</div>
                                <div class="text-muted small">{{ $pay->created_at?->format('M d, Y') }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $pay->member?->name ?? 'Member' }}</div>
                                <div class="text-muted small">{{ $pay->member?->member_code }}</div>
                            </td>
                            <td><span class="sb-badge-pill sb-badge-blue">{{ $pay->payment_type }}</span></td>
                            <td>
                                <div class="fw-bold text-dark">${{ number_format((float) $pay->amount, 2) }}</div>
                                <div class="small text-success">Paid: ${{ number_format((float) $pay->paid_amount, 2) }}</div>
                                @if($pay->due_amount > 0)
                                    <div class="small text-danger fw-bold">Due: ${{ number_format((float) $pay->due_amount, 2) }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $pay->payment_method }}</div>
                                <div class="text-muted small">{{ $pay->transaction_reference }}</div>
                            </td>
                            <td>
                                <span class="sb-badge-pill {{ $pay->payment_status === 'Paid' ? 'sb-badge-green' : ($pay->payment_status === 'Refunded' ? 'sb-badge-blue' : 'sb-badge-amber') }}">
                                    {{ $pay->payment_status }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($pay->payment_status !== 'Paid')
                                        <form action="{{ route('admin.gymfitness.payments.status', $pay->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="payment_status" value="Paid">
                                            <button type="submit" class="btn btn-sm btn-success py-0 px-2" title="Mark Paid">Mark Paid</button>
                                        </form>
                                    @endif
                                    @if($pay->payment_status === 'Paid')
                                        <form action="{{ route('admin.gymfitness.payments.status', $pay->id) }}" method="POST" onsubmit="return confirm('Refund invoice {{ $pay->invoice_no }}?')">
                                            @csrf
                                            <input type="hidden" name="payment_status" value="Refunded">
                                            <button type="submit" class="btn btn-sm btn-outline-warning py-0 px-2" title="Refund">Refund</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.gymfitness.payments.destroy', $pay->id) }}" method="POST" onsubmit="return confirm('Delete invoice {{ $pay->invoice_no }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $payments->links() }}</div>
    </div>

    {{-- Record Payment Modal --}}
    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                <form action="{{ route('admin.gymfitness.payments.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Record Membership / Service Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Select Member *</label>
                                <select name="member_id" class="form-select" required>
                                    @foreach($members as $m)
                                        <option value="{{ $m->id }}">{{ $m->member_code }} — {{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Payment Type *</label>
                                <select name="payment_type" class="form-select" required>
                                    <option value="Membership Fee">Membership Fee</option>
                                    <option value="Registration Fee">Registration Fee</option>
                                    <option value="Renewal Payment">Renewal Payment</option>
                                    <option value="Personal Training">Personal Training</option>
                                    <option value="Additional Service">Additional Service</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Payment Method *</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="bKash">bKash</option>
                                    <option value="Nagad">Nagad</option>
                                    <option value="Visa / MasterCard">Visa / MasterCard</option>
                                    <option value="Cash at Desk">Cash at Desk</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Total Amount ($) *</label>
                                <input type="number" step="0.01" name="amount" value="39" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Discount ($)</label>
                                <input type="number" step="0.01" name="discount_amount" value="0" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Paid Amount ($) *</label>
                                <input type="number" step="0.01" name="paid_amount" value="39" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Payment Status</label>
                                <select name="payment_status" class="form-select">
                                    <option value="Paid">Paid</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Due">Due</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Notes</label>
                                <input type="text" name="notes" class="form-control" placeholder="Optional note">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold px-4">Save Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
