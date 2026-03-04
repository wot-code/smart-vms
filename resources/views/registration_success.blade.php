<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful | Smart VMS</title>
    
    {{-- Auto-refresh every 5 seconds only if the status is still pending --}}
    @if(strtolower($visitor->status) == 'pending')
        <meta http-equiv="refresh" content="5">
    @endif
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .pass-card { max-width: 450px; margin: 50px auto; border: none; border-radius: 20px; overflow: hidden; }
        .pass-header { background: #212529; color: white; padding: 20px; text-align: center; }
        .qr-container { background: white; padding: 20px; display: flex; justify-content: center; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .info-label { font-weight: bold; color: #666; font-size: 0.9rem; }
        .info-value { color: #333; font-weight: 600; }
        .signature-display { max-width: 150px; height: auto; border-bottom: 1px solid #333; }
        
        .status-badge-pending { background-color: #ffc107; color: #000; }
        .status-badge-approved { background-color: #198754; color: #fff; }
        .status-badge-rejected { background-color: #dc3545; color: #fff; }

        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

    <div class="container">
        <div class="card pass-card shadow-lg">
            <div class="pass-header text-uppercase">
                <h4 class="mb-0">Visitor Pass</h4>
                <small>Smart VMS - Security Clearance</small>
            </div>

            <div class="qr-container">
                <div id="qrcode"></div>
            </div>

            <div class="card-body px-4 text-center">
                <h3 class="text-uppercase mb-1">{{ $visitor->full_name }}</h3>
                
                @php
                    // Normalize status to lowercase for reliable checking
                    $currentStatus = strtolower($visitor->status);
                    $badgeClass = 'status-badge-pending';
                    if($currentStatus == 'approved') $badgeClass = 'status-badge-approved';
                    if($currentStatus == 'rejected') $badgeClass = 'status-badge-rejected';
                @endphp
                
                <span class="badge {{ $badgeClass }} px-3 py-2 text-uppercase" style="font-size: 0.9rem;">
                    {{ $visitor->status }}
                </span>

                @if($currentStatus == 'pending')
                    <p class="text-muted small mt-2 no-print italic">
                        Waiting for host approval... <br>
                        <span class="spinner-border spinner-border-sm" role="status"></span>
                        This page updates automatically.
                    </p>
                @endif

                <div class="mt-4 text-start">
                    <div class="info-row">
                        <span class="info-label">Pass ID:</span>
                        <span class="info-value">#VMS-{{ str_pad($visitor->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Host:</span>
                        <span class="info-value">{{ $visitor->host_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Time In:</span>
                        <span class="info-value">{{ date('d M, Y h:i A', strtotime($visitor->check_in)) }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="info-label mb-1">Visitor Signature</p>
                    <img src="{{ $visitor->signature }}" alt="Signature" class="signature-display">
                </div>
            </div>

            <div class="card-footer bg-white border-0 p-4 pt-0 no-print">
                <div class="d-grid gap-2">
                    <button onclick="window.print()" class="btn btn-dark">Print Pass</button>
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary">Back to Welcome</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "VMS-{{ $visitor->id }}",
            width: 160,
            height: 160
        });

        // Backup JavaScript refresh if meta-refresh is slow
        @if(strtolower($visitor->status) == 'pending')
            setTimeout(function(){
                window.location.reload();
            }, 5000);
        @endif
    </script>
</body>
</html>