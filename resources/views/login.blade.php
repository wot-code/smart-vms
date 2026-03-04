<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Smart VMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card shadow-lg border-0" style="width: 100%; max-width: 400px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-dark">Smart VMS</h2>
                <p class="text-muted">Administrator Portal</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger py-2 small">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" required autofocus placeholder="admin@vms.co.ke">
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-dark btn-lg">Login</button>
                </div>
            </form>

            <div class="text-center mt-4">
                <a href="{{ url('/') }}" class="text-decoration-none small text-secondary">← Back to Welcome</a>
            </div>
        </div>
    </div>

</body>
</html>