<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Registration | Smart VMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --vms-primary: #212529; }
        body { background-color: #f8f9fa; font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .registration-card { max-width: 650px; margin: 30px auto; border-radius: 15px; overflow: hidden; }
        .card-header { letter-spacing: 1px; }
        .hidden-fields { display: none; transition: all 0.3s ease; }
        
        /* Improved Signature Canvas */
        .sig-wrapper { position: relative; width: 100%; }
        #sig-canvas { 
            border: 2px dashed #ccc; 
            border-radius: 10px; 
            cursor: crosshair; 
            width: 100%; 
            height: 200px; 
            background-color: #fff; 
            touch-action: none; 
        }
        #sig-canvas:active { border-color: var(--vms-primary); border-style: solid; }
        
        .form-label { font-size: 0.9rem; color: #444; }
        .btn-submit { letter-spacing: 1px; transition: transform 0.2s; }
        .btn-submit:active { transform: scale(0.98); }
    </style>
</head>
<body>
    <div class="container">
        <div class="card registration-card shadow-lg border-0">
            <div class="card-header bg-dark text-white text-center py-4">
                <h3 class="mb-0 fw-bold">VISITOR CHECK-IN</h3>
                <p class="small mb-0 opacity-75">Please provide your details for security clearance</p>
            </div>
            
            <div class="card-body p-4 p-md-5">
                
                {{-- Flash Validation Errors --}}
                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ url('/register-visitor') }}" method="POST" id="vmsForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Full Name *</label>
                            <input type="text" name="full_name" class="form-control form-control-lg" placeholder="e.g. John Mwangi" value="{{ old('full_name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Phone Number *</label>
                            <input type="tel" name="phone" class="form-control form-control-lg" placeholder="0712345678" value="{{ old('phone') }}" required pattern="[0-9+]{10,15}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">ID / Passport Number *</label>
                            <input type="text" name="id_number" class="form-control form-control-lg" placeholder="ID Number" value="{{ old('id_number') }}" required autocomplete="off">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Vehicle Reg (Optional)</label>
                            <input type="text" name="vehicle_reg" class="form-control form-control-lg" placeholder="e.g. KAA 123A" value="{{ old('vehicle_reg') }}" style="text-transform: uppercase;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Visitor Type *</label>
                        <select name="type" id="visitorType" class="form-select form-select-lg" onchange="toggleTypeFields()" required>
                            <option value="Adult" {{ old('type') == 'Adult' ? 'selected' : '' }}>Adult</option>
                            <option value="Minor" {{ old('type') == 'Minor' ? 'selected' : '' }}>Minor</option>
                            <option value="Delivery" {{ old('type') == 'Delivery' ? 'selected' : '' }}>Delivery</option>
                            <option value="Contractor" {{ old('type') == 'Contractor' ? 'selected' : '' }}>Contractor</option>
                        </select>
                    </div>

                    {{-- Minor Fields --}}
                    <div id="minorFields" class="hidden-fields mb-3">
                        <div class="p-3 border-start border-4 border-warning bg-warning-subtle rounded">
                            <label class="form-label text-dark fw-bold">Guardian Name *</label>
                            <input type="text" name="guardian_name" id="guardian_name" class="form-control" placeholder="Parent or Authorized Guardian" value="{{ old('guardian_name') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Host to Visit *</label>
                        <select name="host_name" class="form-select form-select-lg" required>
                            <option value="">-- Select Resident/Office --</option>
                            @if(isset($hosts) && count($hosts) > 0)
                                @foreach($hosts as $host)
                                    <option value="{{ $host->name }}" {{ old('host_name') == $host->name ? 'selected' : '' }}>
                                        {{ $host->name }}
                                    </option>
                                @endforeach
                            @else
                                <option disabled>No Hosts available in system</option>
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Purpose of Visit *</label>
                        <select name="purpose" class="form-select form-select-lg" required>
                            <option value="Official Business" {{ old('purpose') == 'Official Business' ? 'selected' : '' }}>Official Business</option>
                            <option value="Personal Visit" {{ old('purpose') == 'Personal Visit' ? 'selected' : '' }}>Personal Visit</option>
                            <option value="Delivery" {{ old('purpose') == 'Delivery' ? 'selected' : '' }}>Delivery</option>
                            <option value="Maintenance / Contractor" {{ old('purpose') == 'Maintenance / Contractor' ? 'selected' : '' }}>Maintenance / Contractor</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold d-flex justify-content-between">
                            Visitor Signature *
                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0" id="clear-signature">Clear Clear</button>
                        </label>
                        <div class="sig-wrapper">
                            <canvas id="sig-canvas"></canvas>
                        </div>
                        <input type="hidden" name="signature_data" id="signature_data">
                    </div>

                    <div class="form-check mb-4 small text-muted">
                        <input class="form-check-input" type="checkbox" required id="dataConsent">
                        <label class="form-check-label" for="dataConsent">
                            I consent to the collection of my data for security purposes as per the <strong>Kenya Data Protection Act</strong>.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 py-3 btn-submit fw-bold shadow-sm">
                        SUBMIT REGISTRATION
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Signature Pad Script --}}
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    
    <script>
        const canvas = document.getElementById('sig-canvas');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });

        // Smart Resize: Saves the signature before clearing it during resize
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            
            // Save current signature
            const data = signaturePad.toData();
            
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            
            signaturePad.clear(); 
            signaturePad.fromData(data); // Restore signature
        }

        window.onresize = resizeCanvas;
        resizeCanvas();

        document.getElementById('clear-signature').addEventListener('click', () => {
            signaturePad.clear();
        });

        document.getElementById('vmsForm').onsubmit = function(e) {
            if (signaturePad.isEmpty()) {
                alert("Please provide your signature to verify the check-in.");
                e.preventDefault();
                return false;
            }
            // Capture image as Base64
            document.getElementById('signature_data').value = signaturePad.toDataURL('image/png');
        };

        function toggleTypeFields() {
            const type = document.getElementById('visitorType').value;
            const minorFields = document.getElementById('minorFields');
            const guardianInput = document.getElementById('guardian_name');

            if (type === 'Minor') {
                minorFields.style.display = 'block';
                guardianInput.setAttribute('required', 'required');
                guardianInput.focus();
            } else {
                minorFields.style.display = 'none';
                guardianInput.removeAttribute('required');
                guardianInput.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', toggleTypeFields);
    </script>
</body>
</html>