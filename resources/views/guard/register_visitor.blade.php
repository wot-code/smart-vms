@extends('layouts.portal')

@section('content')
<div class="px-6 py-6">

    <div class="max-w-2xl mx-auto space-y-6">

        {{-- ─── Page Header ─── --}}
        <div>
            <h1 class="text-xl font-bold text-[#0a1929]">Assisted Entry Registration</h1>
            <p class="text-sm text-slate-500 mt-0.5">Register walk-in visitors, elderly, or VIPs manually at the gate.</p>
        </div>

        {{-- ─── Form Card ─── --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">

            <div class="flex items-center gap-2 px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                <i class="bi bi-person-badge text-[#0ea5e9]"></i>
                <h2 class="text-sm font-bold text-[#0a1929]">Visitor Details</h2>
            </div>

            <div class="p-6">

                @if($errors->any())
                <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <i class="bi bi-exclamation-circle-fill text-red-500 flex-shrink-0 mt-0.5"></i>
                    <ul class="space-y-0.5">
                        @foreach($errors->all() as $error)
                        <li class="text-sm text-red-700 font-medium">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('guard.store') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <input type="hidden" name="signature" value="MANUAL_ENTRY_GUARD">
                    <input type="hidden" name="type" value="Adult">
                    <input type="hidden" name="status" value="checked_in">

                    {{-- Full Name --}}
                    <div>
                        <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                            Visitor's Full Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="bi bi-person text-slate-400 text-sm"></i>
                            </span>
                            <input type="text" name="full_name" value="{{ old('full_name') }}"
                                   placeholder="e.g. John Doe"
                                   class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                          text-sm text-[#0a1929] font-medium placeholder-slate-400
                                          focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                          transition-all @error('full_name') border-red-300 @enderror" required>
                        </div>
                        @error('full_name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- ID & Phone --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                                ID / Passport <span class="text-slate-400 lowercase font-medium ml-1">(Optional)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="bi bi-card-text text-slate-400 text-sm"></i>
                                </span>
                                <input type="text" name="id_number" value="{{ old('id_number') }}"
                                       placeholder="12345678"
                                       class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                              text-sm text-[#0a1929] font-medium placeholder-slate-400
                                              focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                              transition-all @error('id_number') border-red-300 @enderror">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                                Phone Number <span class="text-slate-400 lowercase font-medium ml-1">(Optional)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="bi bi-telephone text-slate-400 text-sm"></i>
                                </span>
                                <input type="tel" name="phone" value="{{ old('phone') }}"
                                       placeholder="0712 345 678"
                                       class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                              text-sm text-[#0a1929] font-medium placeholder-slate-400
                                              focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                              transition-all @error('phone') border-red-300 @enderror">
                            </div>
                        </div>
                    </div>

                    {{-- Host Selection --}}
                    <div>
                        <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                            Person to Visit (Host) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="bi bi-house-door text-slate-400 text-sm"></i>
                            </span>
                            <select name="host_name"
                                    class="w-full pl-10 pr-8 py-3 bg-white border-2 border-slate-200 rounded-xl
                                           text-sm text-[#0a1929] font-medium appearance-none
                                           focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                           transition-all @error('host_name') border-red-300 @enderror" required>
                                <option value="" disabled {{ old('host_name') ? '' : 'selected' }}>Select Host...</option>
                                @foreach($hosts as $host)
                                    <option value="{{ $host['name'] }}" {{ old('host_name') == $host['name'] ? 'selected' : '' }}>
                                        {{ $host['display'] }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                            </span>
                        </div>
                        @error('host_name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Purpose --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider">
                                Purpose of Visit <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[0.65rem] text-slate-400">Click a tag to autofill</span>
                        </div>
                        
                        <div class="flex flex-wrap gap-2 mb-3">
                            <button type="button" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[0.65rem] font-bold uppercase tracking-wider rounded-lg transition-colors" onclick="fillPurpose('Official Business')">Official</button>
                            <button type="button" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[0.65rem] font-bold uppercase tracking-wider rounded-lg transition-colors" onclick="fillPurpose('Delivery / Courier')">Delivery</button>
                            <button type="button" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[0.65rem] font-bold uppercase tracking-wider rounded-lg transition-colors" onclick="fillPurpose('Maintenance / Repair')">Maintenance</button>
                            <button type="button" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[0.65rem] font-bold uppercase tracking-wider rounded-lg transition-colors" onclick="fillPurpose('Personal Visit')">Personal</button>
                        </div>

                        <textarea name="purpose" id="purposeField" rows="2"
                                  placeholder="Reason for entry..."
                                  class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                         text-sm text-[#0a1929] font-medium placeholder-slate-400
                                         focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                         transition-all @error('purpose') border-red-300 @enderror" required>{{ old('purpose') }}</textarea>
                        @error('purpose') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Vehicle --}}
                    <div>
                        <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                            Vehicle Registration <span class="text-slate-400 lowercase font-medium ml-1">(Leave blank if pedestrian)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="bi bi-car-front text-slate-400 text-sm"></i>
                            </span>
                            <input type="text" name="vehicle_reg" value="{{ old('vehicle_reg') }}"
                                   placeholder="e.g. KAA 001A"
                                   class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                          text-sm text-[#0a1929] font-medium placeholder-slate-400
                                          focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                          transition-all">
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100 mt-6">
                        <a href="{{ route('guard.dashboard') }}"
                           class="text-sm text-slate-500 font-medium hover:text-[#102a43] transition-colors">
                            ← Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-[#102a43] hover:bg-[#0a1929]
                                       text-white text-sm font-bold rounded-xl shadow-md transition-all
                                       focus:outline-none focus:ring-4 focus:ring-[#102a43]/30 active:scale-[0.99] w-full sm:w-auto justify-center">
                            <i class="bi bi-check-circle-fill text-[#0ea5e9]"></i>
                            Confirm & Sign In
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function fillPurpose(text) {
        document.getElementById('purposeField').value = text;
    }
</script>
@endsection