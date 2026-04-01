@extends('layouts.portal')

@section('content')
<div class="px-6 py-6 space-y-6 max-w-6xl mx-auto">

    {{-- ─── Page Header ─── --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-[#0a1929]">User Profile</h1>
            <p class="text-sm text-slate-500 mt-0.5">View details for {{ $user->name }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users_index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 hover:border-slate-300
                      text-[#102a43] text-sm font-semibold rounded-lg transition-all">
                <i class="bi bi-arrow-left"></i>
                <span class="hidden sm:inline">Back to Users</span>
            </a>
            <a href="{{ route('admin.users_edit', $user->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#102a43] hover:bg-[#0a1929]
                      text-white text-sm font-semibold rounded-lg transition-all shadow-sm">
                <i class="bi bi-pencil text-[#0ea5e9]"></i>
                <span class="hidden sm:inline">Edit User</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left: User Details Card --}}
        <div class="lg:col-span-1 border border-slate-200 rounded-2xl bg-white shadow-sm overflow-hidden h-fit">
            
            {{-- Header/Avatar --}}
            <div class="bg-slate-50 border-b border-slate-100 p-8 flex flex-col items-center justify-center text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-b from-[#102a43]/5 to-transparent"></div>
                <div class="w-24 h-24 rounded-full bg-[#102a43] flex items-center justify-center flex-shrink-0 relative z-10 border-4 border-white shadow-md">
                    <span class="text-3xl font-bold text-[#0ea5e9]">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <h2 class="mt-4 text-xl font-extrabold text-[#0a1929] relative z-10">{{ $user->name }}</h2>
                @php
                    [$rbg,$rtxt] = match(strtolower($user->role)) {
                        'admin' => ['bg-[#102a43]','text-white'],
                        'host'  => ['bg-sky-50','text-sky-700 border border-sky-100'],
                        'guard' => ['bg-amber-50','text-amber-700 border border-amber-100'],
                        default => ['bg-slate-100','text-slate-600'],
                    };
                @endphp
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $rbg }} {{ $rtxt }} relative z-10 mt-2">
                    {{ ucfirst($user->role) }}
                </span>
            </div>

            {{-- Info List --}}
            <div class="p-6 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="bi bi-envelope text-slate-400"></i>
                    </div>
                    <div>
                        <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Email Address</p>
                        <p class="text-sm font-medium text-[#0a1929] break-all">{{ $user->email }}</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="bi bi-telephone text-slate-400"></i>
                    </div>
                    <div>
                        <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Phone Number</p>
                        <p class="text-sm font-medium text-[#0a1929] font-mono">{{ $user->phone ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="bi bi-calendar3 text-slate-400"></i>
                    </div>
                    <div>
                        <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Joined Date</p>
                        <p class="text-sm font-medium text-[#0a1929]">{{ $user->created_at->format('F d, Y') }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Visitors (If Host) or Activity (If Admin/Guard) --}}
        <div class="lg:col-span-2">
            
            @if(strtolower($user->role) === 'host')
                <div class="border border-slate-200 rounded-2xl bg-white shadow-sm overflow-hidden flex flex-col h-full min-h-[500px]">
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-people-fill text-[#0ea5e9]"></i>
                            <h2 class="text-base font-bold text-[#0a1929]">Visitor History</h2>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-200 text-slate-700">
                            {{ $visitors->count() }} Records
                        </span>
                    </div>

                    <div class="flex-1 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-white border-b border-slate-100 text-[0.65rem] font-bold text-slate-500 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">Visitor</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4 text-right">View</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($visitors as $v)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="font-bold text-[#0a1929]">{{ $v->full_name }}</p>
                                            <p class="text-xs text-slate-500 truncate max-w-[150px]">{{ $v->purpose }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $s = strtolower(str_replace([' ', '_', '-'], '', $v->status));
                                                [$bg, $text] = match(true) {
                                                    $s === 'approved' => ['bg-emerald-50', 'text-emerald-700'],
                                                    in_array($s, ['inside', 'checkedin']) => ['bg-sky-50', 'text-sky-700'],
                                                    $s === 'pending' => ['bg-amber-50', 'text-amber-700'],
                                                    $s === 'rejected' => ['bg-red-50', 'text-red-700'],
                                                    default => ['bg-slate-100', 'text-slate-600'],
                                                };
                                            @endphp
                                            <span class="inline-flex px-2 py-0.5 rounded text-[0.65rem] font-bold uppercase tracking-wider {{ $bg }} {{ $text }}">
                                                {{ strtoupper($v->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-medium text-slate-700">{{ $v->created_at->format('M d, Y') }}</p>
                                            <p class="text-[0.65rem] text-slate-400 mt-0.5">{{ $v->created_at->format('g:i A') }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('visitor.show', $v->id) }}"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded bg-slate-50 hover:bg-slate-200 text-slate-500 hover:text-[#102a43] transition-colors border border-slate-100"
                                               title="View Pass">
                                                <i class="bi bi-chevron-right text-xs"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-16">
                                            <i class="bi bi-inbox text-5xl text-slate-200 block mb-4"></i>
                                            <p class="text-sm font-bold text-slate-600">No visitors recorded yet.</p>
                                            <p class="text-xs text-slate-400 mt-1">This host has not received any visitors.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="border border-slate-200 rounded-2xl bg-white shadow-sm h-full flex flex-col items-center justify-center p-10 text-center text-slate-500">
                    <i class="bi bi-shield-lock text-6xl text-slate-200 mb-4"></i>
                    <h2 class="text-lg font-bold text-[#0a1929] mb-1">System Operator Account</h2>
                    <p class="text-sm max-w-md">Administrators and Guards do not host visitors directly. They operate and monitor the system.</p>
                    <a href="{{ route('dashboard') }}" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 text-[#102a43] font-bold text-sm tracking-wide border border-slate-200 hover:bg-slate-200 transition-colors">
                        Go to Dashboard <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
