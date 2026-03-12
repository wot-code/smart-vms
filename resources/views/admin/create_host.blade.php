<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User | Smart VMS Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .admin-card { max-width: 600px; margin: 40px auto; border-radius: 16px; overflow: hidden; }
        .form-label { font-size: 0.9rem; color: #4b5563; }
        .form-control, .form-select { padding: 0.75rem; border-radius: 8px; border: 1px solid #d1d5db; }
        .form-control:focus, .form-select:focus { border-color: #4338ca; box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.1); }
        .btn-primary { background-color: #4338ca; border: none; padding: 0.8rem; border-radius: 8px; font-weight: 600; transition: all 0.2s; }
        .btn-primary:hover { background-color: #3730a3; transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0); }
        .input-group-text { cursor: pointer; background: white; border-left: none; }
    </style>
</head>
<body>

<div class="container">
    <div class="card admin-card shadow-lg border-0">
        <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 60px; height: 60px; background: #eef2ff;">
                <i class="bi bi-person-plus text-primary fs-2"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">Add New System User</h4>
            <p class="text-muted small">Create an account for staff, residents, or security guards</p>
        </div>

        <div class="card-body p-4 p-md-5">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FIX: Updated route name to 'admin.store_host' to match web.php --}}
            <form action="{{ route('admin.store_host') }}" method="POST" id="userForm">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           placeholder="e.g. Mitchell Dennis" 
                           value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               placeholder="name@company.com" 
                               value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="tel" name="phone" 
                               class="form-control @error('phone') is-invalid @enderror" 
                               placeholder="0712 345 678" 
                               value="{{ old('phone') }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Account Role</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="" selected disabled>Select a role...</option>
                        <option value="host" {{ old('role') == 'host' ? 'selected' : '' }}>Host / Resident</option>
                        <option value="guard" {{ old('role') == 'guard' ? 'selected' : '' }}>Security Guard</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror" 
                                   required>
                            <span class="input-group-text" onclick="togglePassword('password')">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <button type="submit" class="btn btn-primary w-100 mb-2 shadow-sm" id="submitBtn">
                        <i class="bi bi-check2-circle me-2"></i> REGISTER ACCOUNT
                    </button>
                    <a href="{{ route('admin.users_index') }}" class="btn btn-link w-100 text-muted text-decoration-none small">
                        Cancel and return to list
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Password visibility toggle
    function togglePassword(id) {
        const passwordInput = document.getElementById(id);
        const icon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    // Loading state for form submission
    document.getElementById('userForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
    });
</script>

</body>
</html>