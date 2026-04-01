<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful | Smart VMS</title>
    
    @php
        // Normalize 'checked_in' from the DB to act as 'pending' for this view
        $currentStatus = strtolower($visitor->status);
        $isPending = in_array($currentStatus, ['pending', 'checked_in']);
    @endphp

    {{-- Auto-refresh every 5 seconds if status is pending --}}
    @if($isPending)
        <meta http-equiv="refresh" content="5">
    @endif
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .pass-card { max-width: 450px; margin: 30px auto; border: none; border-radius: 20px; overflow: hidden; }
        .pass-header { background: #212529; color: white; padding: 20px; text-align: center; }
        .qr-container { background: white; padding: 20px; display: flex; justify-content: center; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .info-label { font-weight: bold; color: #666; font-size: 0.85rem; }
        .info-value { color: #333; font-weight: 600; font-size: 0.95rem; }
        .signature-display { max-width: 140px; height: auto; border-bottom: 1px solid #333; padding-bottom: 5px; }
        
        /* Status Badge Colors */
        .status-badge-pending { background-color: #ffc107; color: #000; animation: pulse 1.5s infinite; }
        .status-badge-approved { background-color: #198754; color: #fff; }
        .status-badge-rejected { background-color: #dc3545; color: #fff; }
        .status-badge-inside { background-color: #0d6efd; color: #fff; }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Fix backgrounds not printing */
        @media print { 
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; } 
            .pass-card { margin: 0; box-shadow: none !important; border: 1px solid #ccc; } 
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="card pass-card shadow-lg">
            <div class="pass-header text-uppercase">
                <h4 class="mb-0">Visitor Digital Pass</h4>
                <small class="opacity-75">Smart VMS Security Clearance</small>
            </div>

            <div class="qr-container">
                <div id="qrcode"></div>
            </div>

            <div class="card-body px-4 text-center">
                <h3 class="text-uppercase mb-1 fw-bold">{{ $visitor->full_name }}</h3>
                
                @php
                    $badgeClass = 'status-badge-pending';
                    $displayStatus = 'Pending Approval';
                    
                    if($currentStatus == 'approved') {
                        $badgeClass = 'status-badge-approved';
                        $displayStatus = 'Approved';
                    } elseif($currentStatus == 'rejected') {
                        $badgeClass = 'status-badge-rejected';
                        $displayStatus = 'Rejected';
                    } elseif($currentStatus == 'inside') {
                        $badgeClass = 'status-badge-inside';
                        $displayStatus = 'Checked In (Inside)';
                    }

                    $displayTime = $visitor->created_at->timezone('Africa/Nairobi')->format('h:i A');
                @endphp
                
                <span class="badge {{ $badgeClass }} px-4 py-2 text-uppercase mb-3" style="font-size: 1rem;">
                    {{ $displayStatus }}
                </span>

                @if($isPending)
                    <div class="alert alert-warning py-2 no-print">
                        <small class="fw-bold">Awaiting host approval...</small>
                        <div class="spinner-border spinner-border-sm ms-2" role="status"></div>
                    </div>
                @elseif($currentStatus == 'approved')
                    <div class="alert alert-success py-2 no-print">
                        <small class="fw-bold">Access Granted. Present this to the Guard.</small>
                    </div>
                @endif

                <div class="mt-2 text-start">
                    <div class="info-row">
                        <span class="info-label">Pass ID:</span>
                        <span class="info-value">#VMS-{{ str_pad($visitor->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Host:</span>
                        <span class="info-value">{{ $visitor->host_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Purpose:</span>
                        <span class="info-value">{{ $visitor->purpose }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date & Time:</span>
                        <span class="info-value">{{ $visitor->created_at->timezone('Africa/Nairobi')->format('d M, Y') }} | {{ $displayTime }}</span>
                    </div>
                    
                    @if($currentStatus == 'approved' || $currentStatus == 'inside')
                    <div class="info-row">
                        <span class="info-label text-danger">Valid Until:</span>
                        <span class="info-value text-danger">{{ $visitor->created_at->timezone('Africa/Nairobi')->addHours(4)->format('h:i A') }}</span>
                    </div>
                    @endif
                </div>

                {{-- FIXED: Safely fetch the signature from Laravel Storage --}}
                @if($visitor->signature_path)
                <div class="mt-4">
                    <p class="info-label mb-1">E-Signature</p>
                    <img src="{{ asset('storage/' . $visitor->signature_path) }}" alt="Signature" class="signature-display">
                </div>
                @endif
            </div>

            <div class="card-footer bg-white border-0 p-4 pt-0 no-print">
                <div class="d-grid gap-2">
                    <button onclick="window.print()" class="btn btn-dark fw-bold">Download/Print Pass</button>
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm">Exit to Welcome Screen</a>
                </div>
            </div>
        </div>
        <p class="text-center text-muted small pb-4 no-print">
            &copy; {{ date('Y') }} Smart Visitor Management System.
        </p>
    </div>

    <script>
        // Generate QR Code
        new QRCode(document.getElementById("qrcode"), {
            text: "VMS-PASS-{{ $visitor->id }}",
            width: 180,
            height: 180,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        // Backup JavaScript refresh for Pending status
        @if($isPending)
            setTimeout(function(){
                window.location.reload();
            }, 5000);
        @endif
    </script>
</body>
</html>