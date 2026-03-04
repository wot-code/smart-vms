<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Smart VMS Kenya</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { 
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); 
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
        }
        .hero-card { 
            border: none; 
            border-radius: 24px; 
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            background: white;
            height: 100%;
        }
        .hero-card:hover { 
            transform: translateY(-12px); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important;
        }
        .icon-box {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        .bg-soft-primary { background-color: #e7f1ff; }
        .bg-soft-dark { background-color: #f1f1f1; }
        
        .display-4 {
            letter-spacing: -1px;
        }
        
        .btn-lg {
            border-radius: 12px;
            padding: 15px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body class="d-flex align-items-center">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-dark">Smart VMS</h1>
            <p class="lead text-muted fs-4">Kenya Institutional Visitor Management System</p>
            <div style="width: 60px; height: 4px; background: #0d6efd; margin: 20px auto; border-radius: 2px;"></div>
        </div>

        <div class="row justify-content-center g-4">
            <div class="col-md-5 col-lg-4">
                <div class="card hero-card shadow-lg p-4 text-center">
                    <div class="card-body d-flex flex-column">
                        <div class="icon-box bg-soft-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-person-badge-fill text-primary" viewBox="0 0 16 16">
                                <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm4.5 0a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6m5 2.755C12.146 12.825 10.623 12 8 12s-4.146.826-5 1.755V14a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1z"/>
                            </svg>
                        </div>
                        <h3 class="fw-bold mb-3">Visitor Check-In</h3>
                        <p class="text-muted mb-4">Quick registration for guests and contractors. Notify your host and get your pass instantly.</p>
                        <div class="mt-auto">
                            <a href="{{ route('visitor.register') }}" class="btn btn-primary btn-lg w-100 shadow-sm fw-bold">
                                Start Registration
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5 col-lg-4">
                <div class="card hero-card shadow-lg p-4 text-center">
                    <div class="card-body d-flex flex-column">
                        <div class="icon-box bg-soft-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-shield-lock-fill text-dark" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.8 11.8 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7 7 0 0 0 1.048-.625 11.8 11.8 0 0 0 2.517-2.453c1.678-2.195 2.895-5.513 2.465-9.99a1.54 1.54 0 0 0-1.044-1.263 63 63 0 0 0-2.887-.87C9.843.266 8.69 0 8 0m0 5a1.5 1.5 0 0 1 .5 2.915l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 0 1 8 5"/>
                            </svg>
                        </div>
                        <h3 class="fw-bold mb-3">Staff Portal</h3>
                        <p class="text-muted mb-4">Authorized personnel login to manage host approvals, security logs, and analytics.</p>
                        <div class="mt-auto">
                            <a href="{{ route('login') }}" class="btn btn-dark btn-lg w-100 shadow-sm fw-bold">
                                Staff & Admin Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <p class="small text-muted mb-0">&copy; {{ date('Y') }} Smart VMS Kenya.</p>
            <p class="small text-muted">Institutional Security & Safety Standards Compliant.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>