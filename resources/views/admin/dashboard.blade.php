@extends('layouts.portal')

@section('content')
<div class="px-6 py-6 space-y-6">

    {{-- ─── Page Header ─── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-[#0a1929]">Live System Dashboard</h1>
            <p class="text-sm text-slate-500 mt-0.5">Real-time overview of current check-ins, pending approvals, and system audit logs.</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('admin.create_host') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#102a43] hover:bg-[#0a1929]
                      text-white text-sm font-semibold rounded-lg transition-all">
                <i class="bi bi-person-plus-fill text-[#0ea5e9]"></i>
                Add Host
            </a>
            <a href="{{ route('admin.audit_logs') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200
                      hover:border-slate-300 text-[#102a43] text-sm font-semibold rounded-lg transition-all">
                <i class="bi bi-shield-lock"></i>
                Security Logs
            </a>
        </div>
    </div>

    {{-- Livewire Dashboard Component --}}
    @livewire('admin-dashboard')

</div>
@endsection