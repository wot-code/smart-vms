<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#102a43">
    <title>Visitor Registration | Smart VMS</title>
    <link rel="manifest" href="/manifest.json">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 15px; }
        #signature-pad { 
            cursor: crosshair; 
            touch-action: none; 
            background-color: #fff;
        }
        .form-label { margin-bottom: 0.3rem; }
        .d-none { display: none !important; }

        /* ── Offline toast ── */
        #vms-offline-toast {
            display: none;
            position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%);
            background: #102a43; color: #fff;
            padding: 0.85rem 1.5rem; border-radius: 999px;
            font-size: 0.85rem; font-weight: 600;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
            z-index: 9999; white-space: nowrap;
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateX(-50%) translateY(20px); }
            to   { opacity: 1; transform: translateX(-50%) translateY(0); }
        }
        #vms-offline-toast.show { display: block; }
        #vms-offline-toast.offline-mode { background: #b45309; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">Visitor Registration</h4>
                        <small>Please fill in all details to request entry</small>
                    </div>
                    <div class="card-body p-4">
                        
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 shadow-sm">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('visitor.store') }}" method="POST" id="visitorForm">
                            @csrf
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Full Names <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" placeholder="John Doe" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="e.g., 0712345678" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">ID / Passport Number <span class="text-danger">*</span></label>
                                    <input type="text" name="id_number" class="form-control" value="{{ old('id_number') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Company / Organization</label>
                                    <input type="text" name="organization" class="form-control" value="{{ old('organization') }}" placeholder="Optional">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Visitor Type <span class="text-danger">*</span></label>
                                    <select name="type" id="visitorType" class="form-select" required>
                                        <option value="Adult" {{ old('type') == 'Adult' ? 'selected' : '' }}>Adult</option>
                                        <option value="Minor" {{ old('type') == 'Minor' ? 'selected' : '' }}>Minor</option>
                                    </select>
                                    <div id="minorField" class="mt-2 d-none">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="accompanied" id="accompaniedCheck">
                                            <label class="form-check-label small" for="accompaniedCheck">
                                                Accompanied by Parent/Guardian <span class="text-danger">*</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Host to Visit <span class="text-danger">*</span></label>
                                    <select name="host_name" class="form-select" required>
                                        <option value="" disabled selected>Select Host</option>
                                        @foreach($hosts as $host)
                                            {{-- FIXED: Accessing as array $host['key'] to match Controller --}}
                                            <option value="{{ $host['name'] }}" {{ old('host_name') == $host['name'] ? 'selected' : '' }}>
                                                {{ $host['display'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Purpose of Visit <span class="text-danger">*</span></label>
                                <select name="purpose" id="purposeSelect" class="form-select" required>
                                    <option value="Official Visit">Official Visit</option>
                                    <option value="Delivery">Delivery</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Job Interview">Job Interview</option>
                                    <option value="Other">Other</option>
                                </select>
                                <input type="text" name="purpose_other" id="purposeOther" class="form-control mt-2 d-none" placeholder="Please specify your purpose">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Current Date</label>
                                    <input type="text" class="form-control bg-light" value="{{ date('d M, Y') }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Time In</label>
                                    <input type="time" name="time_in" class="form-control" value="{{ date('H:i') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Vehicle Reg No</label>
                                    <input type="text" name="vehicle_reg" class="form-control" placeholder="KAA 123A">
                                </div>
                            </div>

                            <div class="mb-4 text-center">
                                <label class="form-label fw-bold d-block text-start">Digital Signature <span class="text-danger">*</span></label>
                                <div class="border rounded bg-white p-1">
                                    <canvas id="signature-pad" class="w-100" style="height: 180px;"></canvas>
                                </div>
                                <input type="hidden" name="signature_data" id="signature_data">
                                <div class="mt-2 text-start">
                                    <button type="button" id="clearBtn" class="btn btn-sm btn-outline-secondary">Clear Signature</button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 shadow-sm fw-bold">Submit Registration Request</button>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3 bg-white border-0">
                        <a href="{{ route('login') }}" class="text-decoration-none text-muted small">Staff Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        // 1. Setup Signature Pad
        const canvas = document.getElementById("signature-pad");
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });

        // Handle resizing for mobile
        function resizeCanvas() {
            const ratio =  Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }
        window.onresize = resizeCanvas;
        resizeCanvas();

        document.getElementById('clearBtn').addEventListener('click', () => signaturePad.clear());

        // 2. Handle Conditional Fields
        document.getElementById('visitorType').addEventListener('change', function() {
            const minorField = document.getElementById('minorField');
            const accompaniedCheck = document.getElementById('accompaniedCheck');
            if(this.value === 'Minor') {
                minorField.classList.remove('d-none');
                accompaniedCheck.required = true;
            } else {
                minorField.classList.add('d-none');
                accompaniedCheck.required = false;
            }
        });

        document.getElementById('purposeSelect').addEventListener('change', function() {
            const otherInput = document.getElementById('purposeOther');
            if(this.value === 'Other') {
                otherInput.classList.remove('d-none');
                otherInput.required = true;
            } else {
                otherInput.classList.add('d-none');
                otherInput.required = false;
            }
        });

        // 3. Final Validation
        document.getElementById('visitorForm').addEventListener('submit', function(e) {
            if (signaturePad.isEmpty()) {
                alert("Please provide your signature before submitting.");
                e.preventDefault();
            } else {
                document.getElementById('signature_data').value = signaturePad.toDataURL();
            }
        });
    </script>

    {{-- ═══════════════════════════════════════════════════════════════
         OFFLINE RESILIENCE — Service Worker + IndexedDB Queue
         When offline: saves form to IndexedDB, shows toast.
         On reconnect: auto-syncs queued records to /visitor/offline-sync
    ════════════════════════════════════════════════════════════════════ --}}

    {{-- Offline status toast --}}
    <div id="vms-offline-toast">📡 Offline — Registration saved locally</div>

    <script>
    // ─── 1. Register Service Worker ───────────────────────────────────────────
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => {
                    console.log('[VMS SW] Registered:', reg.scope);
                    // Trigger sync for any queued offline records
                    if ('SyncManager' in window) {
                        reg.sync.register('vms-offline-queue').catch(() => {});
                    }
                })
                .catch(err => console.warn('[VMS SW] Registration failed:', err));
        });
    }

    // ─── 2. IndexedDB Helper ──────────────────────────────────────────────────
    function openVmsDB() {
        return new Promise((resolve, reject) => {
            const req = indexedDB.open('SmartVMS', 1);
            req.onupgradeneeded = e => {
                const db = e.target.result;
                if (!db.objectStoreNames.contains('queue')) {
                    db.createObjectStore('queue', { keyPath: 'id', autoIncrement: true });
                }
            };
            req.onsuccess = e => resolve(e.target.result);
            req.onerror   = e => reject(e.target.error);
        });
    }

    async function saveToOfflineQueue(data) {
        const db = await openVmsDB();
        return new Promise((resolve, reject) => {
            const tx    = db.transaction('queue', 'readwrite');
            const store = tx.objectStore('queue');
            const req   = store.add({ data, savedAt: new Date().toISOString() });
            req.onsuccess = () => resolve(req.result);
            req.onerror   = ()  => reject(req.error);
        });
    }

    // ─── 3. Offline Toast ─────────────────────────────────────────────────────
    const toast = document.getElementById('vms-offline-toast');

    function showToast(msg, isOffline = false) {
        toast.textContent = msg;
        toast.classList.toggle('offline-mode', isOffline);
        toast.classList.add('show');
        clearTimeout(toast._timer);
        toast._timer = setTimeout(() => toast.classList.remove('show'), 5000);
    }

    // Persistent banner when offline
    window.addEventListener('offline', () => {
        toast.textContent = '⚠️  No connection — form will save locally';
        toast.classList.add('show', 'offline-mode');
    });
    window.addEventListener('online', () => {
        toast.classList.remove('offline-mode');
        showToast('✅ Back online — syncing saved records...');
        
        // Background Sync API often delays syncs by minutes to save battery.
        // For an instant presentation demo, we force a manual frontend sync right now:
        manualSync();
        
        navigator.serviceWorker.ready.then(reg => {
            if ('SyncManager' in window) reg.sync.register('vms-offline-queue').catch(() => {});
        });
    });

    // ─── 4. Intercept Form Submit ─────────────────────────────────────────────
    document.getElementById('visitorForm').addEventListener('submit', async function(e) {

        if (!navigator.onLine) {
            e.preventDefault();
            e.stopImmediatePropagation();

            // Must manually validate & inject signature because we stopped propagation
            if (signaturePad.isEmpty()) {
                alert("Please provide your signature before submitting.");
                return;
            }
            document.getElementById('signature_data').value = signaturePad.toDataURL();

            // Collect all form field values
            const formData = new FormData(this);
            const payload  = {};
            formData.forEach((val, key) => { if (key !== '_token') payload[key] = val; });

            try {
                await saveToOfflineQueue(payload);
                showToast('📡 Offline — Registration saved & will sync automatically', true);

                // Reset form
                this.reset();
                signaturePad.clear();
            } catch (err) {
                alert('⚠️ Could not save offline. Please try again.');
                console.error('[VMS Offline Queue]', err);
            }
        }
        // If online: normal submit proceeds
    }, true); // capture phase

    // ─── 5. Manual Sync Fallback (for Safari / older browsers) ───────────────
    async function manualSync() {
        const db    = await openVmsDB();
        const tx    = db.transaction('queue', 'readwrite');
        const store = tx.objectStore('queue');
        const all   = await new Promise((res, rej) => {
            const r = store.getAll();
            r.onsuccess = () => res(r.result);
            r.onerror   = () => rej(r.error);
        });

        let synced = 0;
        for (const record of all) {
            try {
                const resp = await fetch('/visitor/offline-sync', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(record.data)
                });
                if (resp.ok) {
                    const delTx = db.transaction('queue', 'readwrite');
                    delTx.objectStore('queue').delete(record.id);
                    synced++;
                }
            } catch { /* Will retry on next online event */ }
        }
        if (synced > 0) showToast(`✅ ${synced} offline record(s) synced successfully`);
    }

    // Run manual sync immediately in case SW Background Sync isn't supported
    if (navigator.onLine) manualSync();
    </script>
</body>
</html>