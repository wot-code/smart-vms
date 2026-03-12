<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VMS Analytics | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .stat-card { border-radius: 15px; transition: transform 0.2s; border: none !important; }
        .stat-card:hover { transform: translateY(-5px); }
        .chart-container { position: relative; height: 300px; min-height: 300px; }
        .card { border-radius: 12px; border: none; }
        canvas { width: 100% !important; height: auto !important; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">System Analytics</h2>
            <p class="text-muted">Real-time visitor insights for Smart VMS</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-dark shadow-sm">
                <i class="bi bi-printer me-2"></i>Print Report
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-dark px-4 shadow-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row g-3 mb-5">
        <div class="col-md-3">
            <div class="card stat-card shadow-sm p-3 bg-white">
                <small class="text-uppercase fw-bold text-muted">Total Visitors</small>
                <h2 class="fw-bold text-primary mb-0">{{ $stats['total_visitors'] ?? 0 }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm p-3 bg-white border-start border-success border-4">
                <small class="text-uppercase fw-bold text-muted">Today's Check-ins</small>
                <h2 class="fw-bold text-success mb-0">{{ $stats['today_count'] ?? 0 }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm p-3 bg-white border-start border-warning border-4">
                <small class="text-uppercase fw-bold text-muted">Awaiting Approval</small>
                <h2 class="fw-bold text-warning mb-0">{{ $stats['pending_now'] ?? 0 }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm p-3 bg-white border-start border-info border-4">
                <small class="text-uppercase fw-bold text-muted">Approved Today</small>
                <h2 class="fw-bold text-info mb-0">{{ $stats['approved_today'] ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-4 text-secondary">
                    <i class="bi bi-person-badge me-2"></i>Top 5 Busiest Hosts
                </h5>
                <div class="chart-container">
                    <canvas id="hostsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 h-100 text-center">
                <h5 class="fw-bold mb-4 text-secondary">
                    <i class="bi bi-pie-chart-fill me-2"></i>Visitor Categories
                </h5>
                <div class="chart-container">
                    <canvas id="typesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4 text-secondary">
                    <i class="bi bi-graph-up-arrow me-2"></i>Weekly Entry Traffic
                </h5>
                <div style="height: 350px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Set global defaults for Chart.js
    Chart.defaults.font.family = "'Segoe UI', sans-serif";
    Chart.defaults.color = '#6c757d';

    // 1. Top Hosts Chart
    const hostsCtx = document.getElementById('hostsChart').getContext('2d');
    new Chart(hostsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topHosts->pluck('host_name')) !!},
            datasets: [{
                label: 'Visitors',
                data: {!! json_encode($topHosts->pluck('total')) !!},
                backgroundColor: '#212529',
                borderRadius: 8,
                barThickness: 20
            }]
        },
        options: {
            indexAxis: 'y',
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { stepSize: 1 } },
                y: { grid: { display: false } }
            }
        }
    });

    // 2. Visitor Categories Chart
    const typesCtx = document.getElementById('typesChart').getContext('2d');
    new Chart(typesCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($visitorTypes->pluck('type')) !!},
            datasets: [{
                data: {!! json_encode($visitorTypes->pluck('total')) !!},
                backgroundColor: ['#0d6efd', '#ffc107', '#198754', '#dc3545', '#adb5bd'],
                hoverOffset: 15,
                borderWidth: 5,
                borderColor: '#ffffff'
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
            }
        }
    });

    // 3. Weekly Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyTraffic->pluck('label')) !!}, 
            datasets: [{
                label: 'Daily Entry Volume',
                data: {!! json_encode($dailyTraffic->pluck('total')) !!},
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.05)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#0d6efd',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
</body>
</html>