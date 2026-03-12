<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $viewTitle ?? 'VMS Portal' }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root { --vms-primary: #2c3e50; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background-color: var(--vms-primary) !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .main-container { margin-top: 2rem; margin-bottom: 3rem; }
        /* Smooth fade-in for content */
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="bi bi-shield-lock-fill me-2"></i>
                <span>VMS PRO</span>
            </a>
            
            <div class="d-flex align-items-center">
                @auth
                    <span class="text-light me-3 d-none d-md-inline">
                        <small>Logged in as: <strong>{{ Auth::user()->name }}</strong></small>
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm px-3">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container main-container fade-in">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Success Modal
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    timer: 4000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    background: '#ffffff',
                    iconColor: '#28a745',
                });
            @endif

            // 2. Error Modal (for validation or system failures)
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#dc3545'
                });
            @endif

            // 3. Validation Errors Modal (If any)
            @if ($errors->any())
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    html: '<ul class="text-start">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                    confirmButtonColor: '#f39c12'
                });
            @endif
        });
    </script>
    
    @stack('scripts')
</body>
</html>