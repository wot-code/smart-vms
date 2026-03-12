@extends('layouts.app')

@section('content')
<style>
    body { background-color: #f8f9fa; }
    .table-responsive { border-radius: 12px; }
    .card { border: none; border-radius: 15px; overflow: hidden; }
    .badge-ip { font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; background: #f1f3f5; color: #495057; }
    .url-path { color: #dc3545; font-weight: 600; font-family: 'SFMono-Regular', monospace; font-size: 0.85rem; }
    .avatar-sm { width: 35px; height: 35px; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; font-weight: bold; }
    
    /* Pagination Fixes */
    .pagination { margin-bottom: 0; gap: 5px; }
    .page-link { border-radius: 8px !important; margin: 0 2px; border: none; color: #495057; }
    .page-item.active .page-link { background-color: #dc3545; color: white; }
    
    /* Action Badge Style */
    .badge-action { font-size: 0.7rem; letter-spacing: 0.5px; }
</style>

@can('admin-only')
<div class="container py-4">
    {{-- Header Section --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-0">Security Audit Logs</h3>
            <p class="text-muted small">Monitoring unauthorized access and system modifications.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-flex gap-2 justify-content-md-end align-items-center">
                {{-- Corrected route for clearing logs --}}
                <form action="{{ route('admin.security_logs.clear') }}" method="POST" onsubmit="return confirm('WARNING: This will permanently delete all security logs. Proceed?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-trash"></i> Clear Logs
                    </button>
                </form>

                {{-- Corrected route for exporting PDF --}}
                <a href="{{ route('admin.security_logs.export') }}" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Stats and Filter Bar --}}
    <div class="row mb-4 g-3">
        <div class="col-md-8">
            {{-- Corrected route for searching logs --}}
            <form action="{{ route('admin.security_logs') }}" method="GET" class="d-flex gap-2">
                <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden border">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-0" placeholder="Filter by IP, User, or Action..." value="{{ request('search') }}">
                    <button class="btn btn-dark px-3" type="submit">Filter</button>
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.security_logs') }}" class="btn btn-sm btn-light border rounded-pill d-flex align-items-center text-decoration-none">Reset</a>
                @endif
            </form>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="bg-white p-2 px-3 shadow-sm rounded-3 border-start border-danger border-4 d-inline-block text-start">
                <small class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.6rem;">Recorded Incidents</small>
                <span class="h5 fw-bold text-danger mb-0">{{ $logs->total() }}</span>
            </div>
        </div>
    </div>

    {{-- Logs Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small text-uppercase">Time</th>
                            <th class="py-3 text-muted small text-uppercase">Actor</th>
                            <th class="py-3 text-muted small text-uppercase">Action</th>
                            <th class="py-3 text-muted small text-uppercase">Request Path</th>
                            <th class="py-3 text-muted small text-uppercase text-center">Origin IP</th>
                            <th class="pe-4 py-3 text-muted small text-uppercase text-end">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $log->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($log->user)
                                        <div class="avatar-sm bg-soft-danger text-danger rounded-circle me-2 border border-danger border-opacity-25" style="background: #fff5f5;">
                                            {{ strtoupper(substr($log->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $log->user->name }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $log->user->email }}</small>
                                        </div>
                                    @else
                                        <div class="avatar-sm bg-secondary text-white rounded-circle me-2">G</div>
                                        <span class="text-muted small italic">Guest / System</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 badge-action">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>
                            <td>
                                <span class="url-path">{{ parse_url($log->url, PHP_URL_PATH) ?: '/' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-ip border px-2 py-1">{{ $log->ip_address }}</span>
                            </td>
                            <td class="pe-4 text-end">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-secondary border-0" 
                                        data-bs-toggle="popover" 
                                        data-bs-trigger="focus"
                                        title="System Info" 
                                        data-bs-content="{{ $log->user_agent }}">
                                    <i class="bi bi-display text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-shield-check text-success display-2 mb-3"></i>
                                    <h5 class="text-dark fw-bold">System Secure</h5>
                                    <p class="text-muted">No unauthorized access attempts found in the records.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($logs->hasPages())
        <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-between align-items-center">
            <small class="text-muted">Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} logs</small>
            <div>
                {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>
@else
    <div class="container py-5 text-center">
        <div class="alert alert-warning border-0 shadow-sm d-inline-block px-5">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Access Restricted. Redirecting...
        </div>
    </div>
    <script>setTimeout(() => { window.location = "/dashboard"; }, 2000);</script>
@endcan

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
          return new bootstrap.Popover(popoverTriggerEl)
        })
    });
</script>
@endsection