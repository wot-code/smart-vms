<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>{{ $viewTitle }} | Smart VMS</title>
    <style>
        .animate-pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
        .sidebar { height: 100vh; background: #212529; color: white; padding-top: 20px; }
        .nav-link { color: #adb5bd; }
        .nav-link:hover { color: white; }
        .stat-card { transition: transform 0.2s; border: none; }
        .stat-card:hover { transform: translateY(-3px); }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4 shadow">
        <span class="navbar-brand">Smart VMS | {{ auth()->user()->role == 'admin' ? 'Administrator' : 'Host Portal' }}</span>
        <div class="d-flex align-items-center text-white">
            <span class="me-3">Welcome, {{ auth()->user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-light btn-sm">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        {{-- Admin Stats & Analytics Section --}}
        @if(auth()->user()->role === 'admin' && isset($stats))
        <div class="row mb-4 g-3">
            <div class="col-md-2">
                <div class="card stat-card shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75">Today's Arrivals</small>
                        <h3 class="mb-0 fw-bold">{{ $stats['total_today'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card shadow-sm bg-warning text-dark">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75">Pending</small>
                        <h3 class="mb-0 fw-bold">{{ $stats['pending'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card shadow-sm bg-success text-white">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75">Currently Inside</small>
                        <h3 class="mb-0 fw-bold">{{ $stats['inside'] }}</h3>
                    </div>
                </div>
            </div>
            
            {{-- New Analytics Quick-Link Card --}}
            <div class="col-md-3">
                <div class="card stat-card shadow-sm bg-info text-white">
                    <a href="{{ route('admin.analytics') }}" class="text-decoration-none text-white">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-uppercase opacity-75">Detailed Insights</small>
                                <h5 class="mb-0 fw-bold">View Analytics</h5>
                            </div>
                            <i class="bi bi-graph-up-arrow fs-2 opacity-50"></i>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-md-3">
                 <a href="{{ route('admin.host.create') }}" class="btn btn-dark w-100 py-3 shadow-sm d-flex align-items-center justify-content-center h-100">
                    <i class="bi bi-person-plus-fill me-2"></i> Add New Host/Staff
                 </a>
            </div>
        </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">{{ $viewTitle }}</h5>
                <span class="badge bg-secondary rounded-pill px-3">{{ $visitors->count() }} Records Found</span>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm">{{ $errors->first() }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Visitor</th>
                                <th>Host</th>
                                <th>Status</th>
                                <th>Details</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visitors as $v)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $v->full_name }}</div>
                                    <small class="text-muted">{{ $v->phone }}</small>
                                </td>
                                <td><span class="text-muted">{{ $v->host_name }}</span></td>
                                <td>
                                    @if($v->status == 'Pending')
                                        <span class="badge bg-warning text-dark animate-pulse px-3 py-2">PENDING</span>
                                    @elseif($v->status == 'Approved')
                                        <span class="badge bg-success px-3 py-2">APPROVED</span>
                                    @elseif($v->status == 'Rejected')
                                        <span class="badge bg-danger px-3 py-2">REJECTED</span>
                                    @else
                                        <span class="badge bg-secondary px-3 py-2">{{ $v->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('visitor.show', $v->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">View Details</a>
                                </td>
                                <td>
                                    @if($v->status == 'Pending')
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('visitor.approve', $v->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success px-3">Approve</button>
                                            </form>
                                            
                                            <form action="{{ route('visitor.reject', $v->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Deny entry to this visitor?')">Reject</button>
                                            </form>
                                        </div>
                                    @elseif($v->status == 'Approved' && !$v->checked_out_at)
                                        <form action="{{ route('visitor.checkout', $v->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-dark px-4">Check Out</button>
                                        </form>
                                    @elseif($v->status == 'Rejected')
                                        <span class="text-danger small fw-bold">Entry Denied</span>
                                    @else
                                        <span class="text-muted small"><i class="bi bi-check-circle-fill text-success"></i> Completed</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>