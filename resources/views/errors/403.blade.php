<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied | Smart VMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .error-card { max-width: 500px; border: none; border-radius: 15px; text-align: center; padding: 40px; }
        .icon-box { font-size: 80px; color: #dc3545; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="card error-card shadow-lg">
    <div class="card-body">
        <div class="icon-box">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h1 class="fw-bold text-dark">403</h1>
        <h3 class="mb-3 text-secondary">Access Denied</h3>
        <p class="text-muted mb-4">
            Sorry, <strong>{{ auth()->user()->name ?? 'Guest' }}</strong>. 
            You do not have the administrative privileges required to access this section of the Smart VMS.
        </p>
        <div class="d-grid gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-dark py-2 rounded-pill">
                <i class="bi bi-house-door me-2"></i> Return to My Dashboard
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-link text-decoration-none text-muted small">
                    Switch Account
                </button>
            </form>
        </div>
    </div>
    <div class="card-footer bg-transparent border-0 text-muted small">
        &copy; {{ date('Y') }} Smart VMS Security System
    </div>
</div>

</body>
</html>