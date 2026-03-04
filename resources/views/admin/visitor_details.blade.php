<!DOCTYPE html>
<html>
<head>
    <title>Visitor Details | Smart VMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-dark text-white d-flex justify-content-between">
                <h4 class="mb-0">Visitor Information</h4>
                <a href="/dashboard" class="btn btn-sm btn-outline-light">Back to Dashboard</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th>Name:</th><td>{{ $visitor->name }}</td></tr>
                    <tr><th>Phone:</th><td>{{ $visitor->phone }}</td></tr>
                    <tr><th>Visiting Host:</th><td>{{ $visitor->host_name }}</td></tr>
                    <tr><th>Status:</th><td><span class="badge bg-primary">{{ $visitor->status }}</span></td></tr>
                    <tr><th>Check-in Time:</th><td>{{ $visitor->created_at->format('M d, Y H:i A') }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>