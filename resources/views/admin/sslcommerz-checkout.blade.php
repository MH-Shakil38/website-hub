<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSLCommerz Secure Payment Checkout - {{ $payment->gateway_txn_id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="card border-0 shadow-sm p-4" style="max-width: 480px; width: 100%; border-radius: 18px;">
        <div class="text-center mb-3">
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-2">SSLCommerz Secure Gateway</span>
            <h5 class="fw-bold mb-1">{{ $globalSiteName ?? 'SiteBuilder' }} SaaS Checkout</h5>
            <div class="text-muted small">Invoice: <strong>{{ $payment->invoice?->invoice_number }}</strong> • Txn: <code>{{ $payment->gateway_txn_id }}</code></div>
        </div>

        <div class="p-3 rounded-3 bg-light border mb-3">
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Package:</span>
                <span class="fw-bold">{{ $payment->invoice?->plan?->name ?? 'SaaS Subscription' }}</span>
            </div>
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Customer:</span>
                <span>{{ $payment->user?->name }} ({{ $payment->user?->email }})</span>
            </div>
            <div class="d-flex justify-content-between border-top pt-2 mt-2">
                <span class="fw-bold">Payable Amount:</span>
                <span class="fw-bolder text-primary fs-5">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</span>
            </div>
        </div>

        <form action="{{ route('admin.billing.sslcommerz.callback', $payment->id) }}" method="POST" class="d-grid gap-2">
            @csrf
            <input type="hidden" name="outcome" value="success">
            <div class="mb-2">
                <label class="form-label small fw-bold">Select Payment Channel</label>
                <select name="channel" class="form-select">
                    <option value="bKash - SSLCommerz">bKash Mobile Banking</option>
                    <option value="Nagad - SSLCommerz">Nagad Mobile Banking</option>
                    <option value="VISA / MasterCard">VISA / MasterCard / Amex</option>
                    <option value="Rocket / DBBL Nexus">Rocket / DBBL Nexus</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success fw-bold py-2">
                <i class="bi bi-shield-lock-fill me-1"></i> Confirm &amp; Complete Payment ({{ $payment->currency }} {{ number_format($payment->amount, 2) }})
            </button>
        </form>

        <form action="{{ route('admin.billing.sslcommerz.callback', $payment->id) }}" method="POST" class="d-grid mt-2">
            @csrf
            <input type="hidden" name="outcome" value="failed">
            <button type="submit" class="btn btn-sm btn-outline-secondary">Cancel &amp; Return to Dashboard</button>
        </form>
    </div>
</body>
</html>
