<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} - {{ $globalSiteName ?? 'SiteBuilder' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body class="bg-light py-4">
    <div class="container" style="max-width: 820px;">
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <a href="{{ route('admin.billing.index') }}" class="btn btn-sm btn-outline-dark">&larr; Back to Billing</a>
            <button onclick="window.print()" class="btn btn-sm btn-danger fw-bold" style="background: #EC134A; border: none;">Print / Save as PDF</button>
        </div>

        <div class="card border-0 shadow-sm p-4 p-md-5" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                <div>
                    <h3 class="fw-bolder mb-1">{{ $globalSiteName ?? 'SiteBuilder' }} SaaS Platform</h3>
                    <div class="text-muted small">{{ $globalSiteTagline ?? 'Multi-Website SaaS Engine' }}</div>
                </div>
                <div class="text-end">
                    <h4 class="fw-bold mb-1">{{ $invoice->invoice_number }}</h4>
                    <span class="badge {{ $invoice->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }} text-uppercase">
                        {{ $invoice->status }}
                    </span>
                    <div class="text-muted small mt-1">Issued: {{ $invoice->created_at?->format('M d, Y') }}</div>
                    @if($invoice->paid_at)
                        <div class="text-success small fw-semibold">Paid: {{ $invoice->paid_at->format('M d, Y H:i') }}</div>
                    @endif
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-sm-6">
                    <div class="text-uppercase text-muted small fw-bold mb-1">Billed To</div>
                    <div class="fw-bold">{{ $invoice->user?->name }}</div>
                    <div class="text-muted small">{{ $invoice->user?->email }}</div>
                    @if($invoice->user?->phone)
                        <div class="text-muted small">{{ $invoice->user->phone }}</div>
                    @endif
                </div>
                <div class="col-sm-6 text-sm-end">
                    @if($invoice->tenant)
                        <div class="text-uppercase text-muted small fw-bold mb-1">Target Website (Tenant DB)</div>
                        <div class="fw-bold">{{ $invoice->tenant->name }}</div>
                        <div class="text-muted small">Theme: {{ $invoice->tenant->theme_module }} • DB: <code>{{ $invoice->tenant->db_name }}</code></div>
                    @endif
                </div>
            </div>

            <table class="table table-bordered align-middle mb-4">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th class="text-center">Billing Cycle</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $invoice->plan?->name ?? 'SaaS Subscription Plan' }}</div>
                            <div class="text-muted small">{{ $invoice->notes }}</div>
                        </td>
                        <td class="text-center text-uppercase">{{ $invoice->billing_cycle }}</td>
                        <td class="text-end fw-bold">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">Total</th>
                        <th class="text-end fs-5">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</th>
                    </tr>
                </tfoot>
            </table>

            @if($invoice->payments->isNotEmpty())
                <div class="mt-3">
                    <div class="fw-bold small text-uppercase text-muted mb-2">Payment History</div>
                    <table class="table table-sm small mb-0">
                        <thead>
                            <tr>
                                <th>Gateway / Method</th>
                                <th>Transaction ID</th>
                                <th>Status</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->payments as $pm)
                                <tr>
                                    <td>{{ strtoupper($pm->gateway) }} ({{ $pm->method }})</td>
                                    <td><code>{{ $pm->gateway_txn_id }}</code></td>
                                    <td>{{ strtoupper($pm->status) }}</td>
                                    <td class="text-end">{{ $pm->currency }} {{ number_format($pm->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
