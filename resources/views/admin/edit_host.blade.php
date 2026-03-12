<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Host | Smart VMS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .admin-card { max-width: 550px; margin: 50px auto; border-radius: 16px; overflow: hidden; border: none; }
        .form-label { font-size: 0.85rem; color: #4b5563; text-transform: uppercase; letter-spacing: 0.025em; }
        .form-control { padding: 0.75rem; border-radius: 8px; border: 1px solid #d1d5db; transition: all 0.2s; }
        .form-control:focus { border-color: #4338ca; box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.1); }
        .btn-primary { background-color: #4338ca; border: none; padding: 0.8rem; border-radius: 8px; font-weight: 600; transition: transform 0.1s; }
        .btn-primary:hover { background-color: #3730a3; transform: translateY(-1px); }
        .password-section { background-color: #f3f4f6; border: 1px dashed #cbd5e1; }
        .avatar-placeholder { width: 70px; height: 70px; background: #eef2ff; color: #4338ca; font-size: 1.5rem; font-weight: 700; margin: 0 auto 1rem; border: 2px solid #e0e7ff; }
    </style>
</head>
<body>

<div class="container py-3">
    <div class="card admin-card shadow-lg">
        <div class="card-header bg-white border-0 pt-5 pb-0 text-center">
            <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h4 class="fw-bold text-dark mb-1">Edit Host Profile</h4>
            <p class="text-muted small">Update system credentials for: <span class="text-primary fw-semibold">{{ $user->name }}</span></p>
        </div>

        <div class="card-body p-4 p-md-5">
            
            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm small alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="form-label fw-bold">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                        <input type="text" name="name" class="form-control border-start-0 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-bold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-bold">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="phone" class="form-control border-start-0 @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" required>
                        </div>
                    </div>
                </div>

                <div class="password-section p-3 rounded mt-2">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-shield-lock-fill me-2 text-primary"></i>
                        <span class="text-dark small fw-bold">Security & Password</span>
                    </div>
                    <p class="text-muted mb-3" style="font-size: 0.75rem;">Leave password fields blank to keep the current password.</p>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">New Password</label>
                        <input type="password" name="password" id="password" class="form-control form-control-sm @error('password') is-invalid @enderror">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-sm">
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="togglePassword">
                        <label class="form-check-label small text-muted" for="togglePassword">Show passwords</label>
                    </div>
                </div>

                <div class="mt-5">
                    <button type="submit" class="btn btn-primary w-100 mb-3 shadow">
                        <i class="bi bi-check2-circle me-2"></i> SAVE SYSTEM CHANGES
                    </button>
                    <div class="text-center">
                        <a href="{{ route('admin.users.index') }}" class="text-muted text-decoration-none small fw-semibold hover-link">
                            <i class="bi bi-arrow-left me-1"></i> Back to Host List
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Show/Hide Password functionality
    document.getElementById('togglePassword').addEventListener('change', function() {
        const type = this.checked ? 'text' : 'password';
        document.getElementById('password').type = type;
        document.getElementById('password_confirmation').type = type;
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>