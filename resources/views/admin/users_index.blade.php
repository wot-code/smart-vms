@extends('layouts.app')

@section('content')
<style>
    body { background-color: #f8f9fa; font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    .table-container { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: none; }
    
    /* Dynamic Role Badges */
    .badge-host { background-color: #eef2ff; color: #4338ca; font-weight: 600; padding: 0.5em 0.8em; text-transform: uppercase; font-size: 0.75rem; }
    .badge-guard { background-color: #fff7ed; color: #c2410c; font-weight: 600; padding: 0.5em 0.8em; text-transform: uppercase; font-size: 0.75rem; }
    .badge-admin { background-color: #f0fdf4; color: #15803d; font-weight: 600; padding: 0.5em 0.8em; text-transform: uppercase; font-size: 0.75rem; }
    
    .action-btns { display: flex; gap: 8px; justify-content: flex-end; }
    .search-wrapper { position: relative; max-width: 400px; }
    .search-wrapper i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6c757d; }
    .search-wrapper input { padding-left: 35px; border-radius: 8px; border: 1px solid #dee2e6; height: 45px; }
    .search-wrapper input:focus { border-color: #4338ca; box-shadow: 0 0 0 0.2rem rgba(67, 56, 202, 0.15); }
    .host-name { color: #1f2937; font-size: 0.95rem; }
    .table thead th { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.025em; color: #6b7280; }
</style>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            {{-- ERROR FIX: Changed 'admin.dashboard' to 'dashboard' to match typical naming --}}
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active">System Users</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-0 text-dark">System Users</h2>
            <p class="text-muted mb-0">Manage residents, guards, and administrative staff</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-dark px-3">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
            <a href="{{ route('admin.create_host') }}" class="btn btn-primary shadow-sm px-3">
                <i class="bi bi-person-plus-fill me-1"></i> Add New User
            </a>
        </div>
    </div>

    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <div class="search-wrapper shadow-sm">
                <i class="bi bi-search"></i>
                <input type="text" id="hostSearch" class="form-control" placeholder="Search by name, email or phone...">
            </div>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="d-inline-block bg-white border rounded-pill px-4 py-2 shadow-sm small fw-bold">
                Total Records: <span id="hostCount" class="text-primary">{{ $users->total() }}</span>
            </div>
        </div>
    </div>

    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="hostTable">
                <thead>
                    <tr class="border-bottom">
                        <th class="ps-3 py-3">Full Name</th>
                        <th class="py-3">Email Address</th>
                        <th class="py-3">Phone</th>
                        <th class="py-3">Role</th>
                        <th class="py-3">Joined Date</th>
                        <th class="text-end pe-3 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody id="hostTableBody">
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px;">
                                        <span class="small fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="fw-bold host-name">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td class="host-email text-muted small">{{ $user->email }}</td>
                            <td class="host-phone">
                                <span class="badge bg-light text-dark border-0 px-2 font-monospace">{{ $user->phone ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ strtolower($user->role) }}">{{ $user->role }}</span>
                            </td>
                            <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-3">
                                <div class="action-btns">
                                    <a href="{{ route('admin.edit_user', $user->id) }}" class="btn btn-sm btn-light border" title="Edit Profile">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </a>
                                    
                                    {{-- Prevents deleting the logged-in admin --}}
                                    @if($user->id !== Auth::id())
                                    <form action="{{ route('admin.destroy_user', $user->id) }}" method="POST" id="delete-form-{{ $user->id }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-light border" title="Delete Account" onclick="confirmDeleteUser('{{ $user->id }}', '{{ $user->name }}')">
                                            <i class="bi bi-trash3 text-danger"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyStateRow">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people-fill fs-1 d-block mb-3 opacity-25"></i>
                                <p class="mb-0">No users found in the system.</p>
                                <a href="{{ route('admin.create_host') }}" class="btn btn-link btn-sm mt-2">Add your first user</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 px-3 gap-3">
            <small class="text-muted">Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} records</small>
            <div>
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Ensure SweetAlert2 is loaded --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Success Modal
        @if(session('success'))
            Swal.fire({
                title: 'Operation Successful',
                text: "{{ session('success') }}",
                icon: 'success',
                timer: 4000,
                showConfirmButton: false,
                timerProgressBar: true
            });
        @endif

        // Handle Error Modal
        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#4338ca'
            });
        @endif
    });

    // Confirmation for User Deletion
    function confirmDeleteUser(id, name) {
        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete the account for ${name}. This cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Delete',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // Live Table Search
    document.getElementById('hostSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#hostTableBody tr:not(#emptyStateRow):not(#noResultsFound)');
        let visibleCount = 0;

        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            if (text.includes(filter)) {
                row.classList.remove('d-none');
                visibleCount++;
            } else {
                row.classList.add('d-none');
            }
        });

        let tableBody = document.getElementById('hostTableBody');
        let noResults = document.getElementById('noResultsFound');
        
        if (visibleCount === 0 && filter !== "") {
            if (!noResults) {
                let tr = document.createElement('tr');
                tr.id = 'noResultsFound';
                tr.innerHTML = `<td colspan="6" class="text-center py-5">
                    <i class="bi bi-search fs-2 d-block mb-2 text-muted opacity-50"></i>
                    <span class="text-muted">No users matching "<strong>${this.value}</strong>"</span>
                </td>`;
                tableBody.appendChild(tr);
            }
        } else {
            if (noResults) noResults.remove();
        }
    });
</script>
@endpush
@endsection