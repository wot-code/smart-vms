@extends('layouts.portal')

@section('content')
<div class="px-6 py-6 space-y-6">

    {{-- ─── Page Header ─── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-[#0a1929]">Security Audit Trail</h1>
            <p class="text-sm text-slate-500 mt-0.5">Tracking security-sensitive actions for regulatory compliance.</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            @if(Route::has('admin.print_report'))
            <a href="{{ route('admin.print_report', ['source'=>'audit','search'=>request('search'),'date'=>request('date')]) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200
                      hover:border-slate-300 text-[#102a43] text-sm font-semibold rounded-lg transition-all">
                <i class="bi bi-printer"></i>
                Print
            </a>
            @endif
            @if(Route::has('admin.clear_audit_logs'))
            <form action="{{ route('admin.clear_audit_logs') }}" method="POST"
                  onsubmit="return confirm('WARNING: This will permanently delete all audit records. Proceed?');">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-red-200
                               hover:bg-red-50 text-red-600 text-sm font-semibold rounded-lg transition-all">
                    <i class="bi bi-eraser"></i>
                    Clear Trail
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- ─── Filter Bar ─── --}}
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <form action="{{ route('admin.audit_logs') }}" method="GET"
              class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="bi bi-search text-slate-400 text-sm"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search action, description, or admin name..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm
                              text-[#0a1929] placeholder-slate-400 focus:outline-none focus:border-[#102a43]
                              focus:ring-4 focus:ring-[#102a43]/10 transition-all">
            </div>
            <input type="date" name="date" value="{{ request('date') }}"
                   class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm
                          text-[#0a1929] focus:outline-none focus:border-[#102a43] transition-all">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#102a43] hover:bg-[#0a1929]
                           text-white text-sm font-semibold rounded-lg transition-all flex-shrink-0">
                <i class="bi bi-funnel"></i>
                Filter
            </button>
            @if(request('search') || request('date'))
            <a href="{{ route('admin.audit_logs') }}"
               class="inline-flex items-center gap-1 px-4 py-2.5 text-sm text-slate-500 font-medium
                      hover:text-[#102a43] transition-colors">
                <i class="bi bi-x"></i> Clear
            </a>
            @endif
        </form>
    </div>

    {{-- ─── Audit Table ─── --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

        <div class="flex items-center gap-2 px-5 py-4 border-b border-slate-100">
            <i class="bi bi-clipboard-data text-[#0ea5e9]"></i>
            <h2 class="text-sm font-bold text-[#0a1929]">Log Entries</h2>
            <span class="ml-auto text-xs text-slate-400 font-medium">{{ $logs->total() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-36">Timestamp</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">User</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-40">Action</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Description</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-32 hidden lg:table-cell">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-[#0a1929] text-xs">{{ $log->created_at->format('d M Y') }}</p>
                            <p class="text-slate-400 text-xs">{{ $log->created_at->format('h:i A') }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-slate-500">
                                        {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                    </span>
                                </div>
                                <span class="font-medium text-[#0a1929]">{{ $log->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            @php
                            $a = strtoupper($log->action);
                            [$bg, $text] = match(true) {
                                str_contains($a,'DELET') || str_contains($a,'BREACH') => ['bg-red-50','text-red-700'],
                                str_contains($a,'CREAT') || str_contains($a,'ASSIST') => ['bg-emerald-50','text-emerald-700'],
                                str_contains($a,'UPDAT')                               => ['bg-sky-50','text-sky-700'],
                                str_contains($a,'PURGE')                               => ['bg-amber-50','text-amber-700'],
                                default                                                => ['bg-slate-100','text-slate-600'],
                            };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $bg }} {{ $text }}">
                                {{ str_replace('_', ' ', $a) }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-slate-500 text-xs leading-relaxed">{{ $log->description }}</p>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <code class="text-xs font-mono text-[#0ea5e9] bg-sky-50 px-2 py-0.5 rounded">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </code>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <i class="bi bi-shield-check text-5xl text-slate-200 block mb-3"></i>
                            <p class="text-sm font-medium text-slate-400">No security events recorded yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="border-t border-slate-100 px-5 py-3 flex items-center justify-between">
            <p class="text-xs text-slate-400">
                Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}
            </p>
            {{ $logs->appends(request()->input())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection