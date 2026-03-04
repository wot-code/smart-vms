<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VMS Analytics | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card { border-radius: 15px; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
        .chart-container { position: relative; height: 300px; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">System Analytics</h2>
            <p class="text-muted">Real-time visitor insights for Smart VMS</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-dark px-4 shadow-sm">Back to Dashboard</a>
    </div>

    <div class="row g-3 mb-5">
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm p-3 bg-white">
                <small class="text-uppercase fw-bold text-muted">Total Visitors</small>
                <h2 class="fw-bold text-primary mb-0">{{ $stats['total_visitors'] }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm p-3 bg-white border-start border-success border-4">
                <small class="text-uppercase fw-bold text-muted">Today's Check-ins</small>
                <h2 class="fw-bold text-success mb-0">{{ $stats['today_count'] }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm p-3 bg-white border-start border-warning border-4">
                <small class="text-uppercase fw-bold text-muted">Awaiting Approval</small>
                <h2 class="fw-bold text-warning mb-0">{{ $stats['pending_now'] }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm p-3 bg-white border-start border-info border-4">
                <small class="text-uppercase fw-bold text-muted">Approved Today</small>
                <h2 class="fw-bold text-info mb-0">{{ $stats['approved_today'] }}</h2>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-4">Top 5 Busiest Hosts</h5>
                <canvas id="hostsChart"></canvas>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 h-100 text-center">
                <h5 class="fw-bold mb-4">Visitor Categories</h5>
                <div class="chart-container">
                    <canvas id="typesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4">Weekly Entry Traffic (Last 7 Days)</h5>
                <canvas id="trendChart" style="max-height: 350px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Top Hosts (Horizontal Bar Chart looks better for names)
    new Chart(document.getElementById('hostsChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topHosts->pluck('host_name')) !!},
            datasets: [{
                label: 'Visitors Handled',
                data: {!! json_encode($topHosts->pluck('total')) !!},
                backgroundColor: '#212529',
                borderRadius: 5
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } }
        }
    });

    // 2. Visitor Types (Doughnut)
    new Chart(document.getElementById('typesChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($visitorTypes->pluck('type')) !!},
            datasets: [{
                data: {!! json_encode($visitorTypes->pluck('total')) !!},
                backgroundColor: ['#0d6efd', '#ffc107', '#198754', '#dc3545', '#6c757d'],
                borderWidth: 0
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 3. Weekly Trend (Line Chart)
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyTraffic->pluck('date')) !!},
            datasets: [{
                label: 'Daily Check-ins',
                data: {!! json_encode($dailyTraffic->pluck('total')) !!},
                borderColor: '#0d6efd',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                pointBackgroundColor: '#0d6efd'
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>
</body>
</html>