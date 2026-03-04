<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Management | Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .table-container { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .badge-host { background-color: #e9ecef; color: #495057; font-weight: 600; }
        .action-btns { display: flex; gap: 5px; justify-content: flex-end; }
        .search-wrapper { position: relative; max-width: 400px; }
        .search-wrapper i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6c757d; }
        .search-wrapper input { padding-left: 35px; border-radius: 8px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">System Hosts</h2>
            <p class="text-muted">Manage residents and staff who receive visitors</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-dark">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
            <a href="{{ route('admin.host.create') }}" class="btn btn-dark">
                <i class="bi bi-plus-lg"></i> Add New Host
            </a>
        </div>
    </div>

    {{-- Search and Status Summary --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <div class="search-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" id="hostSearch" class="form-control shadow-sm" placeholder="Search by name, email or phone...">
            </div>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <span class="text-muted small">Total Hosts: <strong id="hostCount">{{ $users->count() }}</strong></span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-container border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="hostTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody id="hostTableBody">
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold host-name">{{ $user->name }}</div>
                            </td>
                            <td class="host-email">{{ $user->email }}</td>
                            <td class="host-phone"><code>{{ $user->phone }}</code></td>
                            <td>
                                <span class="badge badge-host">{{ strtoupper($user->role) }}</span>
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-3">
                                <div class="action-btns">
                                    <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-sm btn-outline-dark" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.user.delete', $user->id) }}" method="POST" onsubmit="return confirm('Delete this host?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="noResultsRow">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2"></i>
                                No hosts registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Live Search Functionality
    document.getElementById('hostSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#hostTableBody tr:not(#noResultsRow)');
        let count = 0;

        rows.forEach(row => {
            let name = row.querySelector('.host-name').textContent.toLowerCase();
            let email = row.querySelector('.host-email').textContent.toLowerCase();
            let phone = row.querySelector('.host-phone').textContent.toLowerCase();

            if (name.includes(filter) || email.includes(filter) || phone.includes(filter)) {
                row.style.display = "";
                count++;
            } else {
                row.style.display = "none";
            }
        });

        // Update the visible host count
        document.getElementById('hostCount').textContent = count;
    });
</script>

</body>
</html>