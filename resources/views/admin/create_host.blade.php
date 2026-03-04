<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Host | Smart VMS Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .admin-card { max-width: 550px; margin: 50px auto; border-radius: 15px; }
        .form-control:focus { border-color: #212529; box-shadow: none; }
    </style>
</head>
<body>

<div class="container">
    <div class="card admin-card shadow-lg border-0">
        <div class="card-header bg-dark text-white text-center py-3">
            <h4 class="mb-0">Add New Resident/Host</h4>
            <small class="opacity-75">Create an account for a person who receives visitors</small>
        </div>
        <div class="card-body p-4 p-md-5">

            {{-- Display Success/Error Messages --}}
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.host.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="john@example.com" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" placeholder="+254712345678" value="{{ old('phone') }}" required>
                    <div class="form-text">Use international format for SMS notifications.</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-dark py-2 fw-bold">CREATE HOST ACCOUNT</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary py-2">CANCEL</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>