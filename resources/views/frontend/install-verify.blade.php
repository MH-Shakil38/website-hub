<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email to Complete Theme Installation - {{ $tenant->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-dark text-white d-flex align-items-center justify-content-center min-vh-100 p-3" style="background: radial-gradient(circle at top, #1E1B4B 0%, #090D16 70%);">
    <div class="card border-0 shadow-lg p-4 p-md-5 text-dark" style="max-width: 540px; width: 100%; border-radius: 22px;">
        <div class="text-center mb-3">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-2">
                <i class="bi bi-envelope-check-fill me-1"></i> Step 4 of 4: Email Verification Required
            </span>
            <h4 class="fw-bolder mb-1">Verify Your Email to Install {{ $tenant->theme_module }}</h4>
            <p class="text-muted small mb-0">
                We sent an installation verification link to <strong>{{ $tenant->owner?->email }}</strong>.
                Your dedicated MySQL database will be created immediately after you verify.
            </p>
        </div>

        <div class="p-3 rounded-4 bg-light border mb-4 small">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Website Name:</span>
                <span class="fw-bold">{{ $tenant->name }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Selected Industry Theme:</span>
                <span class="badge bg-danger">{{ $tenant->theme_module }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Reserved Subdomain:</span>
                <code>{{ $tenant->subdomain }}.{{ config('app.central_domains')[0] ?? 'localhost' }}</code>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted">Selected Plan:</span>
                <span class="fw-semibold text-success">{{ $tenant->plan?->name ?? '14-Day Free Trial' }}</span>
            </div>
        </div>

        <!-- Live Installation Progress Box (Shown when clicking Verify & Install Now) -->
        <div id="installProgressBox" class="d-none p-3 rounded-4 bg-dark text-white mb-3">
            <div class="d-flex align-items-center gap-2 fw-bold small mb-2">
                <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                <span id="installStepText">1/3 Creating isolated MySQL database...</span>
            </div>
            <div class="progress" style="height: 7px;">
                <div id="installProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 35%"></div>
            </div>
        </div>

        <form action="{{ $verifyUrl }}" method="POST" id="verifyInstallForm" class="d-grid gap-2">
            @csrf
            <button type="submit" id="btnVerifyAndInstall" class="btn btn-danger btn-lg fw-bold py-3" style="background: #EC134A; border: none; border-radius: 12px;">
                <i class="bi bi-patch-check-fill me-1"></i> Verify Email &amp; Install Theme Now
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ url('/') }}" class="text-muted small text-decoration-none">&larr; Back to SaaS Landing Page</a>
        </div>
    </div>

    <script>
        document.getElementById('verifyInstallForm')?.addEventListener('submit', function () {
            const btn = document.getElementById('btnVerifyAndInstall');
            const box = document.getElementById('installProgressBox');
            const bar = document.getElementById('installProgressBar');
            const txt = document.getElementById('installStepText');

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Installing Theme & Database...';
            box.classList.remove('d-none');

            setTimeout(() => {
                bar.style.width = '70%';
                txt.textContent = '2/3 Running theme migrations & demo content seeder...';
            }, 600);

            setTimeout(() => {
                bar.style.width = '95%';
                txt.textContent = '3/3 Activating 14-day free trial & client dashboard...';
            }, 1300);
        });
    </script>
</body>
</html>
