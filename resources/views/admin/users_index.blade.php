@extends('layouts.portal')

@section('content')
<div class="px-6 py-6 space-y-6">

    {{-- ─── Page Header ─── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-[#0a1929]">System Users</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage hosts, guards, and administrative accounts.</p>
        </div>
        <a href="{{ route('admin.create_host') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-[#102a43] hover:bg-[#0a1929]
                  text-white text-sm font-semibold rounded-lg transition-all flex-shrink-0">
            <i class="bi bi-person-plus-fill text-[#0ea5e9]"></i>
            Add New User
        </a>
    </div>

    {{-- ─── Search + Count ─── --}}
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
        <div class="relative flex-1 max-w-sm">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <i class="bi bi-search text-slate-400 text-sm"></i>
            </span>
            <input type="text" id="userSearch"
                   placeholder="Search name, email or phone..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm
                          text-[#0a1929] placeholder-slate-400 focus:outline-none focus:border-[#102a43]
                          focus:ring-4 focus:ring-[#102a43]/10 transition-all">
        </div>
        <span class="text-sm text-slate-500 font-medium flex-shrink-0">
            <span id="userCount" class="text-[#102a43] font-bold">{{ $users->total() }}</span> users
        </span>
    </div>

    {{-- ─── Users Table ─── --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

        <div class="flex items-center gap-2 px-5 py-4 border-b border-slate-100">
            <i class="bi bi-people text-[#0ea5e9]"></i>
            <h2 class="text-sm font-bold text-[#0a1929]">All Accounts</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="userTable">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Name</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider hidden sm:table-cell">Email</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider hidden md:table-cell">Phone</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Role</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Joined</th>
                        <th class="text-right px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody" class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#102a43] flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-[#0ea5e9]">{{ strtoupper(substr($user->name,0,1)) }}</span>
                                </div>
                                <span class="font-semibold text-[#0a1929]">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 hidden sm:table-cell">
                            <span class="text-slate-500 text-xs">{{ $user->email }}</span>
                        </td>
                        <td class="px-4 py-4 hidden md:table-cell">
                            <span class="text-slate-500 text-xs font-mono">{{ $user->phone ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-4">
                            @php
                            [$rbg,$rtxt] = match(strtolower($user->role)) {
                                'admin' => ['bg-[#102a43]','text-white'],
                                'host'  => ['bg-sky-50','text-sky-700'],
                                'guard' => ['bg-amber-50','text-amber-700'],
                                default => ['bg-slate-100','text-slate-600'],
                            };
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $rbg }} {{ $rtxt }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 hidden lg:table-cell">
                            <span class="text-slate-400 text-xs">{{ $user->created_at->format('M d, Y') }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.users_show', $user->id) }}"
                                   class="w-8 h-8 rounded-lg border border-slate-200 hover:border-[#102a43]
                                          flex items-center justify-center text-slate-400 hover:text-[#102a43] transition-all"
                                   title="View User">
                                    <i class="bi bi-eye text-sm"></i>
                                </a>
                                <a href="{{ route('admin.users_edit', $user->id) }}"
                                   class="w-8 h-8 rounded-lg border border-slate-200 hover:border-[#102a43]
                                          flex items-center justify-center text-slate-400 hover:text-[#102a43] transition-all"
                                   title="Edit User">
                                    <i class="bi bi-pencil text-sm"></i>
                                </a>
                                @if($user->id !== Auth::id())
                                <button onclick="confirmDeleteUser('{{ $user->id }}','{{ addslashes($user->name) }}')"
                                        class="w-8 h-8 rounded-lg border border-slate-200 hover:border-red-300
                                               flex items-center justify-center text-slate-400 hover:text-red-500 transition-all"
                                        title="Delete">
                                    <i class="bi bi-trash3 text-sm"></i>
                                </button>
                                <form id="delete-form-{{ $user->id }}"
                                      action="{{ route('admin.users_destroy', $user->id) }}"
                                      method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <i class="bi bi-people text-5xl text-slate-200 block mb-3"></i>
                            <p class="text-sm font-medium text-slate-400">No users found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="border-t border-slate-100 px-5 py-3">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function confirmDeleteUser(id, name) {
    Swal.fire({
        title: 'Delete user?',
        text: `Remove "${name}" from the system?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#102a43',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete'
    }).then(r => { if (r.isConfirmed) document.getElementById('delete-form-' + id).submit(); });
}

document.getElementById('userSearch').addEventListener('keyup', function() {
    const f = this.value.toLowerCase();
    document.querySelectorAll('#userTableBody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(f) ? '' : 'none';
    });
});
</script>
@endpush