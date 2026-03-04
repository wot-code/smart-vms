<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Registration | Smart VMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Visitor Registration</h4>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success border-0 shadow-sm">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ url('/register-visitor') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="0712345678" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Purpose of Visit</label>
                                <textarea name="purpose" class="form-control" rows="3" placeholder="Meeting with HR" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">Register Visit</button>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3">
                        <a href="{{ url('/login') }}" class="text-decoration-none text-muted small">Admin Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>