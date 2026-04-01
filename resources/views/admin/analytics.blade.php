@extends('layouts.portal')

@section('content')
<div class="px-6 py-6 space-y-6">

    {{-- ─── Page Header ─── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-[#0a1929]">System Analytics</h1>
            <p class="text-sm text-slate-500 mt-0.5">Real-time visitor insights and traffic trends.</p>
        </div>
        <div class="flex-shrink-0">
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200
                           hover:border-slate-300 text-[#102a43] text-sm font-semibold rounded-lg transition-all">
                <i class="bi bi-printer"></i>
                Print Report
            </button>
        </div>
    </div>

    {{-- ─── Stat Cards ─── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label'=>'Total Visitors',   'value'=>$stats['total_visitors']??0, 'icon'=>'bi-people-fill',       'color'=>'text-[#102a43]','bg'=>'bg-slate-100'],
            ['label'=>"Today's Check-ins",'value'=>$stats['today_count']??0,    'icon'=>'bi-arrow-down-circle', 'color'=>'text-emerald-600','bg'=>'bg-emerald-50'],
            ['label'=>'Awaiting Approval','value'=>$stats['pending_now']??0,    'icon'=>'bi-hourglass-split',   'color'=>'text-amber-600',  'bg'=>'bg-amber-50'],
            ['label'=>'Approved Today',   'value'=>$stats['approved_today']??0, 'icon'=>'bi-check-circle-fill', 'color'=>'text-sky-600',    'bg'=>'bg-sky-50'],
        ];
        @endphp
        @foreach($cards as $c)
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $c['label'] }}</p>
                    <p class="text-3xl font-extrabold text-[#0a1929] mt-1">{{ $c['value'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} flex items-center justify-center flex-shrink-0">
                    <i class="bi {{ $c['icon'] }} {{ $c['color'] }} text-base"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ─── Charts Row ─── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Top Hosts --}}
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-5">
                <i class="bi bi-person-badge text-[#0ea5e9]"></i>
                <h2 class="text-sm font-bold text-[#0a1929]">Top 5 Busiest Hosts</h2>
            </div>
            <div style="height: 260px; position: relative;">
                <canvas id="hostsChart"></canvas>
            </div>
        </div>

        {{-- Visitor Categories --}}
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-5">
                <i class="bi bi-pie-chart-fill text-[#0ea5e9]"></i>
                <h2 class="text-sm font-bold text-[#0a1929]">Visitor Categories</h2>
            </div>
            <div style="height: 260px; position: relative;">
                <canvas id="typesChart"></canvas>
            </div>
        </div>

    </div>

    {{-- ─── Weekly Trend ─── --}}
    <div class="bg-white border border-slate-200 rounded-xl p-5">
        <div class="flex items-center gap-2 mb-5">
            <i class="bi bi-graph-up-arrow text-[#0ea5e9]"></i>
            <h2 class="text-sm font-bold text-[#0a1929]">Weekly Entry Traffic</h2>
        </div>
        <div style="height: 280px; position: relative;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#94a3b8';

// Hosts bar chart
new Chart(document.getElementById('hostsChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($topHosts->pluck('host_name')) !!},
        datasets: [{
            label: 'Visitors',
            data: {!! json_encode($topHosts->pluck('total')) !!},
            backgroundColor: '#102a43',
            borderRadius: 6,
            barThickness: 18,
        }]
    },
    options: {
        indexAxis: 'y', maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { grid: { display: false }, ticks: { stepSize: 1 } }, y: { grid: { display: false } } }
    }
});

// Types doughnut
new Chart(document.getElementById('typesChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($visitorTypes->pluck('visitor_type')) !!},
        datasets: [{
            data: {!! json_encode($visitorTypes->pluck('total')) !!},
            backgroundColor: ['#102a43','#0ea5e9','#38bdf8','#334e68','#9fb3c8'],
            hoverOffset: 12, borderWidth: 4, borderColor: '#ffffff'
        }]
    },
    options: {
        maintainAspectRatio: false, cutout: '72%',
        plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } } }
    }
});

// Weekly line chart
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($dailyTraffic->pluck('label')) !!},
        datasets: [{
            label: 'Daily Entries',
            data: {!! json_encode($dailyTraffic->pluck('total')) !!},
            borderColor: '#0ea5e9',
            backgroundColor: 'rgba(14,165,233,0.06)',
            borderWidth: 2.5, tension: 0.4, fill: true,
            pointBackgroundColor: '#0ea5e9', pointRadius: 4, pointHoverRadius: 6
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush