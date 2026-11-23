<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Installed Successfully! - {{ $tenant->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-dark text-white d-flex align-items-center justify-content-center min-vh-100 p-3" style="background: radial-gradient(circle at top, #064E3B 0%, #090D16 70%);">
    <div class="card border-0 shadow-lg p-4 p-md-5 text-dark text-center" style="max-width: 560px; width: 100%; border-radius: 22px;">
        <div class="mb-3">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white shadow" style="width: 64px; height: 64px; font-size: 30px;">
                <i class="bi bi-check-lg"></i>
            </span>
        </div>

        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-bold mx-auto mb-2">
            14-DAY FREE TRIAL ACTIVE • ISOLATED DB READY
        </span>
        <h3 class="fw-bolder mb-2">{{ $tenant->name }} is Live!</h3>
        <p class="text-muted small mb-4">
            Your email has been verified and your <strong>{{ $tenant->theme_module }}</strong> website has been provisioned in its own isolated database (<code>{{ $tenant->db_name }}</code>).
        </p>

        <div class="p-3 rounded-4 bg-light border text-start mb-4 small">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Live Subdomain:</span>
                <a href="http://{{ $primaryDomain }}" target="_blank" class="fw-bold text-primary text-decoration-none">
                    http://{{ $primaryDomain }} <i class="bi bi-box-arrow-up-right"></i>
                </a>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Isolated Database:</span>
                <code class="text-success fw-bold">{{ $tenant->db_name }}</code>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Installed Theme:</span>
                <span class="fw-semibold">{{ $tenant->theme_module }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted">Trial Valid Until:</span>
                <span class="fw-semibold">{{ $tenant->trial_ends_at?->format('M d, Y') ?? now()->addDays(14)->format('M d, Y') }}</span>
            </div>
        </div>

        <div class="d-grid gap-2">
            <a href="{{ route('admin.dashboard', ['tenant_id' => $tenant->id]) }}" class="btn btn-danger btn-lg fw-bold py-3" style="background: #EC134A; border: none; border-radius: 12px;">
                <i class="bi bi-sliders me-1"></i> Open Client Dashboard &amp; Customize Website
            </a>
            <a href="http://{{ $primaryDomain }}" target="_blank" class="btn btn-outline-dark fw-semibold py-2" style="border-radius: 12px;">
                <i class="bi bi-globe2 me-1"></i> Visit Live Website ({{ $primaryDomain }})
            </a>
        </div>
    </div>
</body>
</html>
