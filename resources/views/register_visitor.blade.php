@extends('layouts.app')

@section('content')
<style>
    /* Dark Theme Overrides */
    #sidebar-wrapper, .navbar { display: none !important; }
    main { padding: 0 !important; margin: 0 !important; }
    body { background-color: #0f172a !important; color: #f8fafc; }

    .joint-card {
        background-color: #1e293b;
        border: 1px solid #334155;
        border-radius: 2rem;
        overflow: hidden;
        max-width: 1100px;
    }

    .form-control-vms {
        background-color: #0f172a !important;
        border: 1px solid #334155 !important;
        color: white !important;
    }

    #sig-canvas { 
        border: 2px dashed #475569; 
        border-radius: 10px; 
        background-color: #f8fafc; /* Keep white for clear ink contrast */
        width: 100%; 
        height: 180px; 
    }
</style>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="joint-card shadow-2xl d-flex flex-column flex-lg-row w-100 animate__animated animate__fadeIn">
        
        <div class="p-4 p-md-5 flex-grow-1" x-data="{ started: false }">
            <div x-show="!started" class="text-center py-5">
                <i class="bi bi-person-badge text-info display-1 mb-4"></i>
                <h2 class="fw-bold">Visitor Entry</h2>
                <p class="text-secondary mb-4">Tap below to begin secure check-in.</p>
                <button @click="started = true" class="btn btn-info btn-lg px-5 rounded-pill fw-bold">START REGISTRATION</button>
            </div>

            <div x-show="started" class="animate__animated animate__fadeIn">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="text-info fw-bold"><i class="bi bi-pencil-square me-2"></i>Check-In Form</h4>
                    <span class="badge bg-dark text-info border border-info" id="live_time_display">00:00</span>
                </div>

                <form action="{{ url('/register-visitor') }}" method="POST" id="vmsForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-secondary">Full Name *</label>
                            <input type="text" name="full_name" class="form-control form-control-vms" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-secondary">ID / Passport *</label>
                            <input type="text" name="id_number" class="form-control form-control-vms" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-secondary">Visitor Type</label>
                            <select name="type" id="visitorType" class="form-select form-control-vms" onchange="toggleTypeFields()">
                                <option value="Adult">Adult</option>
                                <option value="Minor">Minor</option>
                                <option value="Contractor">Contractor</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-secondary">Vehicle Reg</label>
                            <input type="text" name="vehicle_reg" class="form-control form-control-vms" placeholder="Optional">
                        </div>
                    </div>

                    <div id="minorFields" class="mt-3" style="display:none;">
                        <div class="p-3 rounded bg-info bg-opacity-10 border border-info border-opacity-25">
                            <label class="small fw-bold text-info">Guardian Name *</label>
                            <input type="text" name="guardian_name" id="guardian_name" class="form-control form-control-vms">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="small fw-bold text-secondary">Host to Visit *</label>
                        <select name="host_name" class="form-select form-control-vms" required>
                             <option value="">Select Host...</option>
                             @foreach($hosts as $host)
                                <option value="{{ $host->name }}">{{ $host->name }}</option>
                             @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <label class="small fw-bold text-secondary d-flex justify-content-between">
                            Signature * <button type="button" class="btn btn-link btn-sm text-info p-0" id="clear-signature">Clear</button>
                        </label>
                        <canvas id="sig-canvas"></canvas>
                        <input type="hidden" name="signature_data" id="signature_data">
                    </div>

                    <button type="submit" class="btn btn-info w-100 mt-4 py-3 fw-bold shadow-lg">SUBMIT CLEARANCE</button>
                </form>
            </div>
        </div>

        <div class="p-4 p-md-5 border-start border-secondary border-opacity-25" style="background-color: #161e2e; min-width: 350px;">
            <div class="text-center mb-5">
                <i class="bi bi-shield-lock text-secondary display-6"></i>
                <h5 class="fw-bold mt-3">Staff Login</h5>
            </div>
            <form action="{{ url('/login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <input type="email" name="email" class="form-control form-control-vms py-3" placeholder="Email">
                </div>
                <div class="mb-4">
                    <input type="password" name="password" class="form-control form-control-vms py-3" placeholder="Password">
                </div>
                <button type="submit" class="btn btn-outline-secondary w-100 py-2">Access Portal</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
    // 1. Clock
    function updateClock() {
        const now = new Date();
        document.getElementById('live_time_display').innerText = now.getHours().toString().padStart(2, '0') + ":" + now.getMinutes().toString().padStart(2, '0');
    }
    setInterval(updateClock, 1000); updateClock();

    // 2. Signature
    const canvas = document.getElementById('sig-canvas');
    const signaturePad = new SignaturePad(canvas);
    document.getElementById('clear-signature').addEventListener('click', () => signaturePad.clear());

    // 3. Form Submit
    document.getElementById('vmsForm').onsubmit = function(e) {
        if (signaturePad.isEmpty()) { alert("Signature required"); e.preventDefault(); return false; }
        document.getElementById('signature_data').value = signaturePad.toDataURL();
    };

    // 4. Toggle Minor
    function toggleTypeFields() {
        const isMinor = document.getElementById('visitorType').value === 'Minor';
        document.getElementById('minorFields').style.display = isMinor ? 'block' : 'none';
    }
</script>
@endsection