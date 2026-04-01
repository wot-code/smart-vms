<div wire:poll.10s class="space-y-6">

    {{-- ─── Stat Cards Row ─── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label'=>'Total Visitors',    'value'=>$stats['total_visitors'], 'icon'=>'bi-people-fill',       'color'=>'text-[#102a43]', 'bg'=>'bg-[#102a43]/10'],
            ['label'=>'Currently Inside',  'value'=>$stats['inside_now'],     'icon'=>'bi-buildings-fill',    'color'=>'text-sky-600',    'bg'=>'bg-sky-50'],
            ['label'=>'Pending Approval',  'value'=>$stats['pending_approval'],'icon'=>'bi-hourglass-split',  'color'=>'text-amber-600',  'bg'=>'bg-amber-50'],
            ['label'=>'Approved Today',    'value'=>$stats['approved_today'], 'icon'=>'bi-check-circle-fill', 'color'=>'text-emerald-600','bg'=>'bg-emerald-50'],
        ];
        @endphp

        @foreach($cards as $card)
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">{{ $card['label'] }}</p>
                    <p class="text-3xl font-extrabold text-[#0a1929] mt-1">{{ $card['value'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl {{ $card['bg'] }} flex items-center justify-center flex-shrink-0">
                    <i class="bi {{ $card['icon'] }} {{ $card['color'] }} text-base"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ─── Main Content Grid Row 1 (Inside & Pending) ─── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        {{-- Currently Inside --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm flex flex-col h-[400px]">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                    <h2 class="text-sm font-bold text-[#0a1929]">Currently Inside</h2>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-sky-100 text-sky-700">{{ $insideNow->count() }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-2">
                @forelse($insideNow as $v)
                    <div class="flex items-center gap-3 p-3 hover:bg-slate-50 rounded-lg transition-colors border-b border-slate-50 last:border-0">
                        <div class="w-9 h-9 rounded-full bg-sky-50 flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-person-badge text-sky-600"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-[#0a1929] truncate">{{ $v->full_name }}</p>
                            <p class="text-[0.65rem] text-slate-500 truncate">Host: {{ $v->host_name }}</p>
                        </div>
                        <div class="text-right flex-shrink-0 flex items-center gap-2">
                            <div class="text-right">
                                <span class="inline-flex px-2 py-0.5 rounded text-[0.65rem] font-bold bg-sky-50 text-sky-700 uppercase tracking-wider">Inside</span>
                                <p class="text-[0.65rem] text-slate-400 font-mono mt-0.5">{{ $v->checked_in_at ? $v->checked_in_at->format('H:i') : 'N/A' }}</p>
                            </div>
                            <a href="{{ route('visitor.show', $v->id) }}" title="View Details"
                               class="inline-flex items-center justify-center w-7 h-7 rounded bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-[#102a43] transition-colors ml-1">
                                <i class="bi bi-eye text-xs"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center p-6">
                        <i class="bi bi-door-closed text-3xl text-slate-200 mb-2"></i>
                        <p class="text-sm font-medium text-slate-500">No visitors currently inside.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pending Approvals --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm flex flex-col h-[400px]">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <h2 class="text-sm font-bold text-[#0a1929]">Awaiting Approval</h2>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">{{ $pendingVisitors->count() }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-2">
                @forelse($pendingVisitors as $v)
                    <div class="flex items-center gap-3 p-3 hover:bg-slate-50 rounded-lg transition-colors border-b border-slate-50 last:border-0">
                        <div class="w-9 h-9 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-amber-600">{{ strtoupper(substr($v->full_name,0,1)) }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-[#0a1929] truncate">{{ $v->full_name }}</p>
                            <p class="text-[0.65rem] text-slate-500 truncate">Waiting for: <span class="font-medium text-slate-700">{{ $v->host_name }}</span></p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-[0.65rem] text-amber-600 font-semibold mb-0.5">{{ $v->created_at->diffForHumans() }}</p>
                            <a href="{{ route('visitor.show', $v->id) }}" class="inline-block px-3 py-1 bg-[#102a43] text-white text-[0.65rem] font-bold rounded-md hover:bg-[#0a1929] transition-colors">Review</a>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center p-6">
                        <i class="bi bi-check2-all text-3xl text-slate-200 mb-2"></i>
                        <p class="text-sm font-medium text-slate-500">All visitors have been processed.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ─── Main Content Grid Row 2 (Recent Flow & Audit Log) ─── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        {{-- Recent Visitor Flow --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm flex flex-col h-[500px]">
            <div class="flex items-center gap-2 px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                <i class="bi bi-node-plus text-[#0ea5e9]"></i>
                <h2 class="text-sm font-bold text-[#0a1929]">Recent Visitor Flow</h2>
            </div>
            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-white shadow-sm ring-1 ring-slate-100 z-10">
                        <tr>
                            <th class="text-left px-4 py-2 text-[0.65rem] font-bold text-slate-400 uppercase tracking-wider">Visitor</th>
                            <th class="text-left px-3 py-2 text-[0.65rem] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="text-right px-4 py-2 text-[0.65rem] font-bold text-slate-400 uppercase tracking-wider">Time</th>
                            <th class="text-right px-4 py-2 text-[0.65rem] font-bold text-slate-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentActivity as $v)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-[#0a1929] text-xs truncate max-w-[150px]">{{ $v->full_name }}</p>
                                    <p class="text-[0.65rem] text-slate-400 truncate max-w-[150px]">{{ $v->purpose }}</p>
                                </td>
                                <td class="px-3 py-3">
                                    @php
                                        $st = strtolower($v->status);
                                        [$stBg, $stColor] = match(true) {
                                            in_array($st, ['approved']) => ['bg-emerald-50', 'text-emerald-700'],
                                            in_array($st, ['inside','checked_in']) => ['bg-sky-50', 'text-sky-700'],
                                            in_array($st, ['pending']) => ['bg-amber-50', 'text-amber-700'],
                                            in_array($st, ['rejected']) => ['bg-red-50', 'text-red-700'],
                                            default => ['bg-slate-100', 'text-slate-600'],
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded text-[0.65rem] font-bold uppercase tracking-wider {{ $stBg }} {{ $stColor }}">
                                        {{ str_replace('_', ' ', $st) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="text-[0.65rem] text-slate-500">{{ $v->created_at->diffForHumans(null, true, true) }} ago</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('visitor.show', $v->id) }}" title="View Details"
                                       class="inline-flex items-center justify-center w-7 h-7 rounded bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-[#102a43] transition-colors">
                                        <i class="bi bi-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-8 text-xs text-slate-400">No recent activity.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-2 border-t border-slate-100 bg-slate-50/50 text-center">
                <a href="{{ route('admin.analytics') }}" class="text-[0.65rem] font-bold text-[#102a43] hover:text-[#0ea5e9] uppercase tracking-wider">View Full Analytics &rarr;</a>
            </div>
        </div>

        {{-- Live System Audit Trail --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm flex flex-col h-[500px]">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <i class="bi bi-shield-check text-[#0ea5e9]"></i>
                    <h2 class="text-sm font-bold text-[#0a1929]">Live System Audit</h2>
                </div>
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                @forelse($recentAudit as $log)
                    <div class="relative pl-4 border-l-2 border-slate-100 pb-1">
                        {{-- Dot --}}
                        <div class="absolute -left-[5px] top-1.5 w-2 h-2 rounded-full bg-[#102a43] ring-4 ring-white"></div>
                        
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <p class="text-[0.65rem] font-bold text-slate-500 uppercase tracking-widest">{{ $log->user->name ?? 'System' }}</p>
                            <span class="text-[0.65rem] text-slate-400 font-mono">{{ $log->created_at->format('H:i:s') }}</span>
                        </div>
                        
                        <div class="bg-slate-50 border border-slate-100 rounded-lg p-2.5">
                            <div class="flex items-center gap-2 mb-1">
                                @php
                                    $a = strtoupper($log->action);
                                    [$bg, $text] = match(true) {
                                        str_contains($a,'DELET') || str_contains($a,'BREACH') => ['bg-red-100','text-red-700'],
                                        str_contains($a,'CREAT') || str_contains($a,'ASSIST') => ['bg-emerald-100','text-emerald-700'],
                                        str_contains($a,'UPDAT') => ['bg-sky-100','text-sky-700'],
                                        str_contains($a,'PURGE') => ['bg-amber-100','text-amber-700'],
                                        default => ['bg-slate-200','text-slate-700'],
                                    };
                                @endphp
                                <span class="inline-flex px-1.5 py-0.5 rounded text-[0.6rem] font-bold {{ $bg }} {{ $text }}">
                                    {{ str_replace('_', ' ', $a) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 leading-snug">{{ $log->description }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-slate-400">No audit logs available.</div>
                @endforelse
            </div>
            <div class="px-4 py-2 border-t border-slate-100 bg-slate-50/50 text-center">
                <a href="{{ route('admin.audit_logs') }}" class="text-[0.65rem] font-bold text-[#102a43] hover:text-[#0ea5e9] uppercase tracking-wider">Explore Audit Trail &rarr;</a>
            </div>
        </div>

    </div>
</div>
