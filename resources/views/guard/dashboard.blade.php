@extends('layouts.portal')

@section('content')
<div class="px-6 py-6 space-y-6">

    {{-- ─── Page Header ─── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-[#0a1929]">Security Desk</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage visitor check-ins, monitor active visitors, and verify exits.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Gate Operations Live
            </span>
            <a href="{{ route('guard.register') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#102a43] hover:bg-[#0a1929]
                      text-white text-sm font-semibold rounded-lg transition-all">
                <i class="bi bi-person-plus-fill text-[#0ea5e9]"></i>
                Manual Entry
            </a>
        </div>
    </div>

    {{-- ─── Stat Cards ─── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @php
        $cards = [
            ['label'=>'Expected Today',   'value'=>$stats['expected_count'] ?? 0,    'icon'=>'bi-person-badge',   'color'=>'text-[#102a43]',   'bg'=>'bg-[#102a43]/10'],
            ['label'=>'Currently Inside', 'value'=>$stats['inside_count'] ?? 0,      'icon'=>'bi-buildings-fill', 'color'=>'text-sky-600',     'bg'=>'bg-sky-50'],
            ['label'=>'Exits Logged',     'value'=>$stats['checked_out_today'] ?? 0, 'icon'=>'bi-door-open-fill', 'color'=>'text-slate-600',   'bg'=>'bg-slate-100'],
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

    {{-- ─── Search Bar ─── --}}
    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center gap-4">
        <form action="{{ route('guard.dashboard') }}" method="GET" class="flex-1 flex gap-2">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="bi bi-search text-slate-400"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Find visitor by Name or ID Number..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-[#0a1929] transition-all focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-[#102a43] text-sm font-bold rounded-lg transition-colors border border-slate-200 border-b-slate-300">
                Search
            </button>
            @if(request('search'))
            <a href="{{ route('guard.dashboard') }}" class="px-3 py-2.5 text-slate-400 hover:text-red-500 transition-colors flex items-center justify-center">
                <i class="bi bi-x-circle-fill text-lg"></i>
            </a>
            @endif
        </form>
    </div>

    {{-- ─── Main Content Grid ─── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        {{-- Expected (Waiting Check-in) --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm flex flex-col h-[500px]">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <i class="bi bi-clock-history text-[#0ea5e9]"></i>
                    <h2 class="text-sm font-bold text-[#0a1929]">Expected / Waiting</h2>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">{{ count($expected) }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-2">
                @forelse($expected as $v)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 hover:bg-slate-50 rounded-lg transition-colors border-b border-slate-50 last:border-0">
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#102a43]/5 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-[#102a43]">{{ strtoupper(substr($v->full_name,0,1)) }}</span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-[#0a1929]">{{ $v->full_name }}</h3>
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[0.6rem] font-bold bg-amber-50 text-amber-700 uppercase tracking-widest">{{ $v->status }}</span>
                                </div>
                                <div class="text-[0.7rem] text-slate-500 space-y-0.5">
                                    <p><i class="bi bi-person-badge text-slate-400 mr-1"></i> Host: <strong class="text-slate-700">{{ $v->host->name ?? $v->host_name }}</strong></p>
                                    @if($v->id_number)<p><i class="bi bi-card-text text-slate-400 mr-1"></i> ID: {{ $v->id_number }}</p>@endif
                                    <p class="text-[#0ea5e9]"><i class="bi bi-chat-left-text mr-1"></i> {{ Str::limit($v->purpose, 30) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <form action="{{ route('guard.checkin', $v->id) }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200
                                       text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-1.5">
                                    Check In <i class="bi bi-arrow-right"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center p-6">
                        <i class="bi bi-inbox text-4xl text-slate-200 mb-3"></i>
                        <p class="text-sm font-bold text-[#0a1929]">No expected visitors.</p>
                        <p class="text-xs text-slate-500 mt-1">All registered visitors have been cleared.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Currently Inside --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm flex flex-col h-[500px]">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                    <h2 class="text-sm font-bold text-[#0a1929]">Currently Inside</h2>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-sky-100 text-sky-700">{{ count($inside) }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-2">
                @forelse($inside as $v)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 hover:bg-slate-50 rounded-lg transition-colors border-b border-slate-50 last:border-0">
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-full bg-sky-50 flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-geo-alt-fill text-sky-600"></i>
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-sm font-bold text-[#0a1929]">{{ $v->full_name }}</h3>
                                <div class="text-[0.7rem] text-slate-500 space-y-0.5">
                                    <p><i class="bi bi-telephone text-slate-400 mr-1"></i> {{ $v->phone ?? 'No phone' }}</p>
                                    <p><i class="bi bi-car-front text-slate-400 mr-1"></i> {{ $v->vehicle_reg ?: 'Pedestrian' }}</p>
                                    <p class="text-[#0ea5e9] font-mono mt-1 pt-1 border-t border-slate-100 w-fit">
                                        <i class="bi bi-clock-history mr-1"></i> Entered: {{ $v->checked_in_at ? $v->checked_in_at->format('H:i') : $v->created_at->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <form action="{{ route('guard.checkout', $v->id) }}" method="POST" onsubmit="return confirm('Confirm check-out for {{ $v->full_name }}?');">
                                @csrf @method('PUT')
                                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-50 hover:bg-slate-100 text-[#102a43] border border-slate-200
                                       text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-1.5">
                                    Check Out <i class="bi bi-box-arrow-right"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center p-6">
                        <i class="bi bi-door-closed text-4xl text-slate-200 mb-3"></i>
                        <p class="text-sm font-bold text-[#0a1929]">Facility is empty.</p>
                        <p class="text-xs text-slate-500 mt-1">There are no checked-in visitors currently inside.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection