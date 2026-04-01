@extends('layouts.portal')

@section('content')
<div class="px-6 py-6">

    <div class="max-w-2xl mx-auto space-y-6">

        {{-- ─── Page Header ─── --}}
        <div>
            <h1 class="text-xl font-bold text-[#0a1929]">Add New User</h1>
            <p class="text-sm text-slate-500 mt-0.5">Create an account for a host, guard, or administrator.</p>
        </div>

        {{-- ─── Form Card ─── --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

            <div class="flex items-center gap-2 px-5 py-4 border-b border-slate-100">
                <i class="bi bi-person-plus text-[#0ea5e9]"></i>
                <h2 class="text-sm font-bold text-[#0a1929]">Account Details</h2>
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

                <form action="{{ route('admin.store_host') }}" method="POST" id="userForm" class="space-y-5">
                    @csrf

                    {{-- Full Name --}}
                    <div>
                        <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="bi bi-person text-slate-400 text-sm"></i>
                            </span>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   placeholder="e.g. Mitchell Dennis"
                                   class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                          text-sm text-[#0a1929] font-medium placeholder-slate-400
                                          focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                          transition-all @error('name') border-red-300 @enderror" required>
                        </div>
                        @error('name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email + Phone --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="bi bi-envelope text-slate-400 text-sm"></i>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       placeholder="name@company.com"
                                       class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                              text-sm text-[#0a1929] font-medium placeholder-slate-400
                                              focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                              transition-all @error('email') border-red-300 @enderror" required>
                            </div>
                            @error('email') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                                Phone <span class="text-red-500">*</span>
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
                                              transition-all @error('phone') border-red-300 @enderror" required>
                            </div>
                            @error('phone') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                            Account Role <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="bi bi-shield-check text-slate-400 text-sm"></i>
                            </span>
                            <select name="role"
                                    class="w-full pl-10 pr-8 py-3 bg-white border-2 border-slate-200 rounded-xl
                                           text-sm text-[#0a1929] font-medium appearance-none
                                           focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                           transition-all @error('role') border-red-300 @enderror" required>
                                <option value="" disabled selected>Select a role...</option>
                                <option value="host"  {{ old('role')=='host'  ? 'selected':'' }}>Host / Resident</option>
                                <option value="guard" {{ old('role')=='guard' ? 'selected':'' }}>Security Guard</option>
                                <option value="admin" {{ old('role')=='admin' ? 'selected':'' }}>Administrator</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                            </span>
                        </div>
                        @error('role') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Password + Confirm --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="bi bi-key text-slate-400 text-sm"></i>
                                </span>
                                <input type="password" id="password" name="password"
                                       class="w-full pl-10 pr-10 py-3 bg-white border-2 border-slate-200 rounded-xl
                                              text-sm text-[#0a1929] font-medium
                                              focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                              transition-all @error('password') border-red-300 @enderror" required>
                                <button type="button" onclick="togglePwd('password','eyeIcon1')"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                    <i class="bi bi-eye text-sm" id="eyeIcon1"></i>
                                </button>
                            </div>
                            @error('password') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="bi bi-key text-slate-400 text-sm"></i>
                                </span>
                                <input type="password" id="passwordConfirm" name="password_confirmation"
                                       class="w-full pl-10 pr-10 py-3 bg-white border-2 border-slate-200 rounded-xl
                                              text-sm text-[#0a1929] font-medium
                                              focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                              transition-all" required>
                                <button type="button" onclick="togglePwd('passwordConfirm','eyeIcon2')"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                    <i class="bi bi-eye text-sm" id="eyeIcon2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        <a href="{{ route('admin.users_index') }}"
                           class="text-sm text-slate-500 font-medium hover:text-[#102a43] transition-colors">
                            ← Cancel
                        </a>
                        <button type="submit" id="submitBtn"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#102a43] hover:bg-[#0a1929]
                                       text-white text-sm font-bold rounded-xl transition-all
                                       focus:outline-none focus:ring-4 focus:ring-[#102a43]/30 active:scale-[0.99]">
                            <i class="bi bi-check-circle-fill text-[#0ea5e9]"></i>
                            Register Account
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePwd(id, iconId) {
    const i = document.getElementById(id);
    const icon = document.getElementById(iconId);
    i.type = i.type === 'password' ? 'text' : 'password';
    icon.className = i.type === 'password' ? 'bi bi-eye text-sm' : 'bi bi-eye-slash text-sm';
}
document.getElementById('userForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Processing...';
});
</script>
@endpush