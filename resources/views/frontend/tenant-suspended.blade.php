<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Temporarily Unavailable - {{ $tenant->name ?? 'SaaS Hub' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-sm border-0 p-4 text-center" style="max-width: 480px; border-radius: 16px;">
        <div class="mb-3">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Status: {{ ucfirst($tenant->status ?? 'Unavailable') }}</span>
        </div>
        <h4 class="fw-bold mb-2">{{ $tenant->name ?? 'This Website' }} is Currently Unavailable</h4>
        <p class="text-muted small mb-4">
            This website is either pending verification, under maintenance, or its subscription/trial period has expired.
            If you are the site owner, please log in to your dashboard to activate your website.
        </p>
        <div>
            <a href="{{ url('/login') }}" class="btn btn-primary px-4">Owner Dashboard Login</a>
        </div>
    </div>
</body>
</html>
