<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Smart VMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --sidebar-width: 260px; }
        body { background-color: #f4f7f6; overflow-x: hidden; font-family: 'Segoe UI', Tahoma, sans-serif; }
        
        /* Sidebar Styling */
        .sidebar { 
            width: var(--sidebar-width); 
            height: 100vh; 
            position: fixed; 
            background: #1a1d20; 
            color: white; 
            z-index: 1000;
        }
        .main-content { margin-left: var(--sidebar-width); padding: 2rem; }
        
        .nav-link { 
            color: #adb5bd; 
            padding: 0.8rem 1rem; 
            margin: 0.2rem 0.5rem; 
            border-radius: 8px; 
        }
        .nav-link:hover, .nav-link.active { background: #343a40; color: #fff; }
        .nav-link i { margin-right: 10px; }

        /* Dashboard Cards */
        .stat-card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <div class="text-center mb-4">
        <h4 class="fw-bold text-primary">SMART VMS</h4>
        <small class="text-muted text-uppercase">Admin Portal</small>
    </div>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="{{ route('dashboard') }}" class="nav-link active"><i class="bi bi-speedometer2"></i> System Overview</a></li>
        <li><a href="{{ route('admin.users_index') }}" class="nav-link"><i class="bi bi-people"></i> Manage Hosts</a></li>
        <li><a href="{{ route('admin.analytics') }}" class="nav-link"><i class="bi bi-bar-chart-line"></i> Web Analytics</a></li>
        <li><a href="{{ route('admin.security_logs') }}" class="nav-link"><i class="bi bi-shield-check"></i> Security Logs</a></li>
    </ul>
    <hr>
    <div class="px-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-outline-danger w-100 btn-sm"><i class="bi bi-box-arrow-left me-2"></i>Logout</button>
        </form>
    </div>
</div>

<div class="main-content">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Global Activity Monitor</h2>
            <p class="text-muted">Real-time oversight of all visitors and hosts.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.create_host') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="bi bi-person-plus-fill me-2"></i>Add New Host
            </a>
            <a href="{{ route('admin.analytics') }}" class="btn btn-dark shadow-sm rounded-pill px-4">
                <i class="bi bi-graph-up me-2"></i>Full Analytics
            </a>
        </div>
    </header>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card stat-card p-3 border-start border-primary border-5">
                <small class="text-muted fw-bold">TOTAL VISITORS</small>
                <h3 class="fw-bold mb-0">{{ $stats['total_visitors'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3 border-start border-warning border-5">
                <small class="text-muted fw-bold">PENDING APPROVAL</small>
                <h3 class="fw-bold mb-0">{{ $stats['pending_approval'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3 border-start border-success border-5">
                <small class="text-muted fw-bold">APPROVED TODAY</small>
                <h3 class="fw-bold mb-0 text-success">{{ $stats['approved_today'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3 border-start border-info border-5">
                <small class="text-muted fw-bold">ACTIVE HOSTS</small>
                <h3 class="fw-bold mb-0 text-info">{{ $stats['active_hosts'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Visitor Activities</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Visitor</th>
                            <th>Host Assigned</th>
                            <th>Status</th>
                            <th>Purpose</th>
                            <th>Logged At</th>
                            <th class="text-end pe-4">System Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visitors as $v)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $v->full_name }}</div>
                                <small class="text-muted">{{ $v->phone }}</small>
                            </td>
                            <td><span class="fw-semibold text-dark">{{ $v->host_name }}</span></td>
                            <td>
                                @php
                                    $badgeClass = match($v->status) {
                                        'Approved' => 'bg-success text-white',
                                        'Pending'  => 'bg-warning text-dark',
                                        'Rejected' => 'bg-danger text-white',
                                        'Inside'   => 'bg-info text-white',
                                        default    => 'bg-secondary text-white'
                                    };
                                @endphp
                                <span class="badge rounded-pill {{ $badgeClass }}">
                                    {{ $v->status }}
                                </span>
                            </td>
                            <td>{{ Str::limit($v->purpose, 30) }}</td>
                            <td>{{ $v->created_at->diffForHumans() }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('visitor.show', $v->id) }}" class="btn btn-sm btn-light border" title="View Details">
                                    <i class="bi bi-eye text-primary"></i>
                                </a>
                                <form action="{{ route('admin.visitor.destroy', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-light border text-danger" title="Delete Log">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox text-muted fs-1"></i>
                                <p class="text-muted mt-2">No recent system activity found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($visitors->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $visitors->links() }}
        </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>