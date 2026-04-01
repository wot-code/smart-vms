{{-- =====================================================================
     SMART VMS — Livewire Visitor Registration Form
     Modern, responsive, pure Tailwind v4 — no Bootstrap classes
     ===================================================================== --}}

<div>

    {{-- Validation Errors --}}
    @if ($errors->any())
    <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <i class="bi bi-exclamation-circle-fill text-red-500 text-base flex-shrink-0 mt-0.5"></i>
        <ul class="space-y-0.5">
            @foreach ($errors->all() as $error)
            <li class="text-sm text-red-700 font-medium">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="vmsForm" onsubmit="return false;" class="space-y-6">

        {{-- ─── SECTION: Personal Info ─── --}}
        <div>
            <h3 class="text-xs font-bold text-[#102a43] uppercase tracking-widest mb-4 flex items-center gap-2">
                <span class="w-5 h-px bg-[#0ea5e9] inline-block"></span>
                Personal Information
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Full Name --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="bi bi-person text-slate-400 text-sm"></i>
                        </span>
                        <input type="text" wire:model.blur="full_name"
                               placeholder="e.g. Ndegwa Wilson"
                               class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                      text-sm text-[#0a1929] font-medium placeholder-slate-400
                                      focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                      transition-all @error('full_name') border-red-300 @enderror">
                    </div>
                    @error('full_name')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="bi bi-telephone text-slate-400 text-sm"></i>
                        </span>
                        <input type="text" wire:model.blur="phone"
                               placeholder="07xx or +254xx"
                               class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                      text-sm text-[#0a1929] font-medium placeholder-slate-400
                                      focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                      transition-all @error('phone') border-red-300 @enderror">
                    </div>
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ID / Passport --}}
                <div>
                    <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                        ID / Passport No <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="bi bi-credit-card text-slate-400 text-sm"></i>
                        </span>
                        <input type="text" wire:model.blur="id_number"
                               placeholder="e.g. 12345678"
                               class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                      text-sm text-[#0a1929] font-medium placeholder-slate-400
                                      focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                      transition-all @error('id_number') border-red-300 @enderror">
                    </div>
                    @error('id_number')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Visitor Type (pill radio) --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-2">
                        Visitor Type <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-3">
                        @foreach(['Adult', 'Minor'] as $typeOption)
                        <label class="flex-1 relative cursor-pointer">
                            <input type="radio" wire:model.live="type"
                                   value="{{ $typeOption }}"
                                   class="sr-only peer">
                            <div class="flex items-center justify-center gap-2 py-3 px-4
                                        border-2 rounded-xl text-sm font-semibold
                                        border-slate-200 text-slate-500 bg-white
                                        peer-checked:border-[#102a43] peer-checked:bg-[#102a43]
                                        peer-checked:text-white transition-all duration-150 cursor-pointer
                                        hover:border-[#102a43]/50">
                                <i class="bi bi-{{ $typeOption === 'Adult' ? 'person-fill' : 'person-hearts' }} text-base"></i>
                                {{ $typeOption }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        {{-- ─── SECTION: Visit Details ─── --}}
        <div>
            <h3 class="text-xs font-bold text-[#102a43] uppercase tracking-widest mb-4 flex items-center gap-2">
                <span class="w-5 h-px bg-[#0ea5e9] inline-block"></span>
                Visit Details
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Purpose --}}
                <div>
                    <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                        Purpose of Visit <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="bi bi-briefcase text-slate-400 text-sm"></i>
                        </span>
                        <select wire:model.live="purpose"
                                class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                       text-sm text-[#0a1929] font-medium appearance-none
                                       focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                       transition-all @error('purpose') border-red-300 @enderror">
                            <option value="">Select Purpose</option>
                            <option value="Delivery">Delivery</option>
                            <option value="Official Visit">Official Visit</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Job Interview">Job Interview</option>
                            <option value="Other">Other</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                        </span>
                    </div>
                    @if($purpose === 'Other')
                    <input type="text" wire:model.blur="purpose_other"
                           placeholder="Please specify..."
                           class="mt-2 w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                  text-sm text-[#0a1929] font-medium placeholder-slate-400
                                  focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                  transition-all">
                    @endif
                </div>

                {{-- Host --}}
                <div>
                    <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                        Host / Employee <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="bi bi-building text-slate-400 text-sm"></i>
                        </span>
                        <select wire:model="host_id"
                                class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                       text-sm text-[#0a1929] font-medium appearance-none
                                       focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                       transition-all @error('host_id') border-red-300 @enderror">
                            <option value="">Select Host</option>
                            @foreach($hosts as $host)
                            <option value="{{ $host->id }}">{{ $host->name }}</option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                        </span>
                    </div>
                    @error('host_id')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Vehicle Reg --}}
                <div>
                    <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                        Vehicle Reg <span class="text-slate-400 font-normal normal-case">(optional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="bi bi-truck text-slate-400 text-sm"></i>
                        </span>
                        <input type="text" wire:model.blur="vehicle_reg"
                               placeholder="e.g. KAA 123A"
                               class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                      text-sm text-[#0a1929] font-medium placeholder-slate-400
                                      focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                      transition-all uppercase">
                    </div>
                </div>

                {{-- Time In --}}
                <div>
                    <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                        Time In <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="bi bi-clock text-slate-400 text-sm"></i>
                        </span>
                        <input type="time" wire:model="time_in"
                               class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                      text-sm text-[#0a1929] font-medium
                                      focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                      transition-all @error('time_in') border-red-300 @enderror">
                    </div>
                    @error('time_in')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ─── SECTION: Signature ─── --}}
        <div wire:ignore>
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold text-[#102a43] uppercase tracking-widest flex items-center gap-2">
                    <span class="w-5 h-px bg-[#0ea5e9] inline-block"></span>
                    Visitor Signature <span class="text-red-500 ml-1">*</span>
                </h3>
                <button type="button" id="clearBtn"
                        class="text-xs font-semibold text-slate-500 hover:text-red-600
                               flex items-center gap-1 transition-colors">
                    <i class="bi bi-arrow-counterclockwise"></i> Clear
                </button>
            </div>

            <div id="signature-wrapper"
                 class="relative bg-white border-2 border-dashed border-slate-300 rounded-xl overflow-hidden"
                 style="height: 200px;">
                <canvas id="signature-pad"
                        class="absolute inset-0 w-full h-full"
                        style="touch-action: none; cursor: crosshair;"></canvas>
                <p id="sig-hint"
                   class="absolute inset-0 flex items-center justify-center text-sm text-slate-400 font-medium pointer-events-none">
                    Draw your signature here
                </p>
            </div>
            @error('signature')
                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- ─── SUBMIT ─── --}}
        <button type="button" id="submitBtn"
                class="w-full flex items-center justify-center gap-2
                       bg-[#102a43] hover:bg-[#0a1929] text-white
                       font-bold text-sm rounded-xl py-4
                       transition-all duration-150 focus:outline-none
                       focus:ring-4 focus:ring-[#102a43]/30 active:scale-[0.99]">
            <i class="bi bi-check-circle-fill text-[#0ea5e9]"></i>
            Complete Check-In
        </button>

    </form>

    {{-- Signature Pad Library --}}
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    @script
    <script>
        const canvas  = document.getElementById('signature-pad');
        const wrapper = document.getElementById('signature-wrapper');
        const hint    = document.getElementById('sig-hint');
        let signaturePad;

        function setupSignature() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width  = canvas.offsetWidth  * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);

            if (!signaturePad) {
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255,255,255)',
                    penColor: 'rgb(16, 42, 67)', // navy pen
                    minWidth: 1.5,
                    maxWidth: 3,
                });
                // Hide hint once drawing starts
                signaturePad.addEventListener('beginStroke', () => {
                    if (hint) hint.style.display = 'none';
                });
            }
        }

        const observer = new ResizeObserver(() => {
            if (canvas.offsetWidth > 0) setupSignature();
        });
        observer.observe(wrapper);

        document.getElementById('clearBtn').addEventListener('click', () => {
            signaturePad?.clear();
            if (hint) hint.style.display = '';
        });

        document.getElementById('submitBtn').addEventListener('click', () => {
            if (!signaturePad || signaturePad.isEmpty()) {
                // Shake the signature wrapper briefly
                wrapper.style.borderColor = '#ef4444';
                wrapper.style.borderStyle = 'solid';
                setTimeout(() => {
                    wrapper.style.borderColor = '';
                    wrapper.style.borderStyle = 'dashed';
                }, 1500);
                return;
            }
            $wire.set('signature', signaturePad.toDataURL(), false);
            $wire.submit();
        });
    </script>
    @endscript

</div>