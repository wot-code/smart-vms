<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Details | Smart VMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 12px; border: none; }
        .detail-label { font-weight: 600; color: #6c757d; width: 35%; }
        .status-badge { padding: 0.5rem 1rem; border-radius: 50px; font-weight: 600; }
        .signature-box { background: #fff; border: 1px dashed #ccc; border-radius: 8px; padding: 10px; text-align: center; }
        .signature-box img { max-width: 100%; height: auto; max-height: 150px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-info-circle me-2 text-primary"></i>Visitor Profile
                    </h5>
                    {{-- FIX: Use the generic dashboard route to accommodate both Admins and Hosts --}}
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>
                
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h2 class="fw-bold mb-1">{{ $visitor->full_name }}</h2>
                            <p class="text-muted"><i class="bi bi-person-badge me-2"></i>ID: {{ $visitor->id_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            @php
                                $statusClass = match($visitor->status) {
                                    'Approved', 'Inside' => 'bg-success',
                                    'Pending'  => 'bg-warning text-dark',
                                    'Rejected' => 'bg-danger',
                                    'Checked Out' => 'bg-secondary',
                                    default    => 'bg-info'
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $visitor->status }}</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <tbody>
                                <tr>
                                    <td class="detail-label">Contact Phone</td>
                                    <td>{{ $visitor->phone }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Visiting Host</td>
                                    <td><strong>{{ $visitor->host_name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Purpose of Visit</td>
                                    <td>{{ $visitor->purpose }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Vehicle Registration</td>
                                    <td><span class="badge bg-light text-dark border">{{ $visitor->vehicle_reg ?? 'No Vehicle' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Check-in Log</td>
                                    <td>{{ $visitor->created_at->format('M d, Y') }} at {{ $visitor->created_at->format('H:i A') }}</td>
                                </tr>
                                @if($visitor->checked_out_at)
                                <tr>
                                    <td class="detail-label">Check-out Log</td>
                                    <td class="text-danger">{{ \Carbon\Carbon::parse($visitor->checked_out_at)->format('M d, Y - H:i A') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if($visitor->signature)
                    <div class="mt-4">
                        <p class="detail-label mb-2">Visitor Signature</p>
                        <div class="signature-box">
                            <img src="{{ $visitor->signature }}" alt="Visitor Signature">
                        </div>
                    </div>
                    @endif
                </div>

                <div class="card-footer bg-light border-0 py-3 text-center">
                    @if($visitor->status === 'Pending')
                        <div class="btn-group w-100">
                            <form action="{{ route('visitor.approve', $visitor->id) }}" method="POST" class="w-100 me-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 rounded-pill">Approve Entry</button>
                            </form>
                            <form action="{{ route('visitor.reject', $visitor->id) }}" method="POST" class="w-100">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100 rounded-pill">Deny Entry</button>
                            </form>
                        </div>
                    @else
                        <p class="mb-0 text-muted italic">This request has already been processed.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>