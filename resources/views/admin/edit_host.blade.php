<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Host | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 500px;">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Edit Host: {{ $user->name }}</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" required>
                </div>

                <div class="bg-light p-3 rounded mb-3">
                    <small class="text-muted d-block mb-2">Leave password fields blank to keep current password.</small>
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control mb-2">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-dark">UPDATE HOST</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-link text-muted">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>