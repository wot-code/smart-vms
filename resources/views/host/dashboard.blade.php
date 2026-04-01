@extends('layouts.portal')

@section('content')
<div class="px-6 py-6 space-y-6">

    {{-- ─── Page Header ─── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-[#0a1929]">My Visitors Portal</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage your expected visitors, approve requests, and view history.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                System Live
            </span>
        </div>
    </div>

    {{-- ─── Stat Cards ─── --}}
    @if(isset($stats))
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label'=>'Total Visitors',   'value'=>$stats['total_visitors'],   'icon'=>'bi-people-fill',       'color'=>'text-[#102a43]',   'bg'=>'bg-[#102a43]/10'],
            ['label'=>'Pending Approval', 'value'=>$stats['pending_approval'], 'icon'=>'bi-hourglass-split',  'color'=>'text-amber-600',   'bg'=>'bg-amber-50'],
            ['label'=>'Inside Now',       'value'=>$stats['inside_now'],       'icon'=>'bi-buildings-fill',    'color'=>'text-sky-600',     'bg'=>'bg-sky-50'],
            ['label'=>'Approved Today',   'value'=>$stats['approved_today'],   'icon'=>'bi-check-circle-fill', 'color'=>'text-emerald-600', 'bg'=>'bg-emerald-50'],
        ];
        @endphp
        @foreach($cards as $c)
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">{{ $c['label'] }}</p>
                    <p class="text-3xl font-extrabold text-[#0a1929] mt-1">{{ $c['value'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} flex items-center justify-center flex-shrink-0">
                    <i class="bi {{ $c['icon'] }} {{ $c['color'] }} text-base"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ─── Visitor Records Table ─── --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="flex items-center gap-2">
                <i class="bi bi-list-ul text-[#0ea5e9]"></i>
                <h2 class="text-sm font-bold text-[#0a1929]">{{ $viewTitle ?? 'Visitor Records' }}</h2>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                {{ $visitors->total() }} Records
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-[0.65rem] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3">Visitor</th>
                        <th class="px-5 py-3">Host</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($visitors as $v)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-slate-600">{{ strtoupper(substr($v->full_name ?? $v->visitor_name ?? '?', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-[#0a1929]">{{ $v->full_name ?? $v->visitor_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $v->phone }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-medium text-slate-600">{{ $v->host_name }}</span>
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $s = strtolower(str_replace([' ', '_', '-'], '', $v->status));
                                [$bg, $text, $dot] = match(true) {
                                    $s === 'approved' => ['bg-emerald-50', 'text-emerald-700', 'bg-emerald-500'],
                                    in_array($s, ['inside', 'checkedin']) => ['bg-sky-50', 'text-sky-700', 'bg-sky-500'],
                                    $s === 'pending' => ['bg-amber-50', 'text-amber-700', 'bg-amber-500'],
                                    $s === 'rejected' => ['bg-red-50', 'text-red-700', 'bg-red-500'],
                                    default => ['bg-slate-100', 'text-slate-600', 'bg-slate-400'],
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[0.65rem] font-bold uppercase tracking-wider {{ $bg }} {{ $text }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dot }} {{ $s === 'pending' ? 'animate-pulse' : '' }}"></span>
                                {{ strtoupper($v->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('visitor.show', $v->id) }}" title="View Details"
                                   class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-500 flex items-center justify-center transition-colors border border-slate-200">
                                    <i class="bi bi-eye"></i>
                                </a>

                                @if($s === 'pending')
                                    <form action="{{ route('host.visitor.accept', $v->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" title="Approve" class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 flex items-center justify-center transition-colors border border-emerald-200">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('host.visitor.reject', $v->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" title="Reject" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition-colors border border-red-200">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                @elseif(in_array($s, ['inside', 'checkedin', 'approved']))
                                    <form action="{{ route('host.visitor.checkout', $v->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" title="Check Out" class="px-3 h-8 rounded-lg bg-[#102a43] hover:bg-[#0a1929] text-white text-xs font-semibold flex items-center justify-center transition-colors">
                                            Check Out
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-10">
                            <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                                <i class="bi bi-inbox text-2xl text-slate-300"></i>
                            </div>
                            <p class="text-sm font-semibold text-slate-600">No visitors found.</p>
                            <p class="text-xs text-slate-400 mt-1">System searched for "{{ auth()->user()->name }}".</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($visitors->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-white">
            {{ $visitors->links('pagination::tailwind') }}
        </div>
        @endif
    </div>

</div>
@endsection