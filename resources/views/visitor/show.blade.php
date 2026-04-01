@extends('layouts.portal')

@section('content')
<div class="px-6 py-6">

    <div class="max-w-4xl mx-auto space-y-6">

        {{-- ─── Page Header ─── --}}
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-[#0a1929]">Visitor Pass</h1>
                <p class="text-sm text-slate-500 mt-0.5">Digital record and access details for {{ $visitor->full_name }}</p>
            </div>
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 hover:border-slate-300
                      text-[#102a43] text-xs font-bold uppercase tracking-wider rounded-lg transition-all hidden sm:flex">
                <i class="bi bi-arrow-left"></i>
                <span class="mt-0.5">Back</span>
            </a>
        </div>

        {{-- ─── Main Pass Card ─── --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            
            {{-- Status Banner Header --}}
            @php
                $s = strtolower(str_replace([' ', '_', '-'], '', $visitor->status));
                [$bg, $text_color, $icon] = match(true) {
                    $s === 'approved' => ['bg-emerald-500', 'text-white', 'document-check'],
                    in_array($s, ['inside', 'checkedin']) => ['bg-sky-500', 'text-white', 'geo-alt-fill'],
                    $s === 'pending' => ['bg-amber-400', 'text-amber-900', 'hourglass-split'],
                    $s === 'rejected' => ['bg-red-500', 'text-white', 'x-circle-fill'],
                    default => ['bg-slate-400', 'text-white', 'shield-slash'],
                };
            @endphp
            <div class="{{ $bg }} {{ $text_color }} px-8 py-6 flex items-center justify-between relative overflow-hidden">
                <div class="absolute right-0 top-0 opacity-10">
                    <i class="bi bi-person-badge" style="font-size: 8rem; margin-top:-2rem; margin-right:-1rem;"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold uppercase tracking-widest opacity-80 mb-1">Current Status</p>
                    <h2 class="text-3xl font-extrabold tracking-tight">{{ strtoupper($visitor->status) }}</h2>
                </div>
                <div class="relative z-10 hidden sm:block">
                    <i class="bi bi-{{ $icon }} text-5xl opacity-80"></i>
                </div>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                    
                    {{-- Left Col: Visitor Info --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <i class="bi bi-person-circle text-[#0ea5e9]"></i>
                            <h3 class="text-sm font-bold text-[#0a1929] uppercase tracking-wider">Visitor Records</h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Full Name</p>
                                <p class="text-base font-semibold text-[#0a1929]">{{ $visitor->full_name }}</p>
                            </div>
                            <div>
                                <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Phone Number</p>
                                <p class="text-sm font-medium text-slate-700">{{ $visitor->phone ?: 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-0.5">National ID / Passport</p>
                                <p class="text-sm font-medium text-slate-700">{{ $visitor->id_number ?: 'Not Provided' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Right Col: Visit Details --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <i class="bi bi-card-text text-[#0ea5e9]"></i>
                            <h3 class="text-sm font-bold text-[#0a1929] uppercase tracking-wider">Visit Logistics</h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Host Name</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-md bg-slate-100 flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-user-tie text-slate-500 text-[0.65rem]"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-[#0a1929] capitalize">{{ $visitor->host_name }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Purpose of Visit</p>
                                <p class="text-sm font-medium text-slate-700">{{ $visitor->purpose }}</p>
                            </div>
                            <div>
                                <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Vehicle</p>
                                <p class="text-sm font-medium text-slate-700">
                                    @if($visitor->vehicle_reg)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-slate-100 border border-slate-200 mt-1 font-mono text-xs font-bold text-slate-700 uppercase">
                                            <i class="bi bi-car-front-fill text-slate-400"></i> {{ $visitor->vehicle_reg }}
                                        </span>
                                    @else
                                        Pedestrian Walk-in
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Horizontal Divider --}}
                <div class="h-px w-full bg-slate-100 my-8"></div>

                {{-- Action Log & Signatures --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 items-center">
                    
                    {{-- Check In --}}
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-box-arrow-in-right text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Signed In</p>
                            <p class="text-sm font-bold text-[#0a1929] mt-0.5">
                                {{ $visitor->checked_in_at ? \Carbon\Carbon::parse($visitor->checked_in_at)->format('d M, g:i A') : '---' }}
                            </p>
                        </div>
                    </div>

                    {{-- Check Out --}}
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-box-arrow-right text-slate-600"></i>
                        </div>
                        <div>
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Signed Out</p>
                            <p class="text-sm font-bold text-[#0a1929] mt-0.5">
                                {{ $visitor->checked_out_at ? \Carbon\Carbon::parse($visitor->checked_out_at)->format('d M, g:i A') : '---' }}
                            </p>
                        </div>
                    </div>

                    {{-- Digital Signature --}}
                    <div class="text-center sm:text-right">
                        @if($visitor->signature && $visitor->signature !== 'MANUAL_ENTRY_GUARD')
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Visitor Signature</p>
                            <div class="inline-block p-2 rounded-xl bg-slate-50 border-2 border-dashed border-slate-200">
                                <img src="{{ $visitor->signature }}" alt="Signature" class="max-h-12 object-contain filter grayscale opacity-75 mix-blend-multiply">
                            </div>
                        @elseif($visitor->signature === 'MANUAL_ENTRY_GUARD')
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Entry Method</p>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-50 text-sky-700 border border-sky-100 font-bold text-[0.65rem] uppercase tracking-wider">
                                <i class="bi bi-shield-check"></i> Manual Guard Entry
                            </div>
                        @else
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Signature</p>
                            <p class="text-xs font-semibold text-slate-300 italic">No signature on file</p>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Pending Actions Block for Host/Admin --}}
            @if($s === 'pending' && (Auth::user()->role === 'admin' || strtolower(Auth::user()->name) === strtolower($visitor->host_name)))
                <div class="bg-amber-50 border-t border-amber-100 p-6 flex flex-col sm:flex-row gap-3">
                    <form action="{{ route('visitor.approve', $visitor->id) }}" method="POST" class="flex-1">
                        @csrf @method('PUT')
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-sm transition-all text-sm uppercase tracking-wider">
                            <i class="bi bi-check-circle-fill"></i> Approve Access
                        </button>
                    </form>
                    <form action="{{ route('visitor.reject', $visitor->id) }}" method="POST" class="flex-1">
                        @csrf @method('PUT')
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-white border-2 border-red-200 hover:border-red-300 hover:bg-red-50 text-red-600 font-bold rounded-xl transition-all text-sm uppercase tracking-wider">
                            <i class="bi bi-x-circle-fill"></i> Deny
                        </button>
                    </form>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection