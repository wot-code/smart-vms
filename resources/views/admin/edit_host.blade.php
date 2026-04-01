@extends('layouts.portal')

@section('content')
<div class="px-6 py-6">

    <div class="max-w-2xl mx-auto space-y-6">

        {{-- ─── Page Header ─── --}}
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-[#102a43] flex items-center justify-center flex-shrink-0">
                <span class="text-lg font-extrabold text-[#0ea5e9]">{{ strtoupper(substr($user->name,0,1)) }}</span>
            </div>
            <div>
                <h1 class="text-xl font-bold text-[#0a1929]">Edit User</h1>
                <p class="text-sm text-slate-500">Updating credentials for <strong>{{ $user->name }}</strong></p>
            </div>
        </div>

        {{-- ─── Form Card ─── --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

            <div class="flex items-center gap-2 px-5 py-4 border-b border-slate-100">
                <i class="bi bi-pencil-square text-[#0ea5e9]"></i>
                <h2 class="text-sm font-bold text-[#0a1929]">Profile Details</h2>
            </div>

            <div class="p-6 space-y-5">

                @if($errors->any())
                <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl p-4">
                    <i class="bi bi-exclamation-circle-fill text-red-500 flex-shrink-0 mt-0.5"></i>
                    <ul class="space-y-0.5">
                        @foreach($errors->all() as $error)
                        <li class="text-sm text-red-700 font-medium">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('admin.update_user', $user->id) }}" method="POST" class="space-y-5">
                    @csrf @method('PUT')

                    {{-- Name --}}
                    <div>
                        <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">Full Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="bi bi-person text-slate-400 text-sm"></i>
                            </span>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                          text-sm text-[#0a1929] font-medium
                                          focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                          transition-all @error('name') border-red-300 @enderror" required>
                        </div>
                        @error('name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email + Phone --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">Email</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="bi bi-envelope text-slate-400 text-sm"></i>
                                </span>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                              text-sm text-[#0a1929] font-medium
                                              focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                              transition-all @error('email') border-red-300 @enderror" required>
                            </div>
                            @error('email') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-1.5">Phone</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="bi bi-telephone text-slate-400 text-sm"></i>
                                </span>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                       class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                              text-sm text-[#0a1929] font-medium
                                              focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                              transition-all @error('phone') border-red-300 @enderror" required>
                            </div>
                            @error('phone') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Password section --}}
                    <div class="bg-slate-50 border border-dashed border-slate-200 rounded-xl p-4 space-y-4">
                        <div>
                            <p class="text-xs font-bold text-[#102a43] uppercase tracking-wider">Security & Password</p>
                            <p class="text-xs text-slate-400 mt-0.5">Leave blank to keep the current password.</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">New Password</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <i class="bi bi-key text-slate-400 text-sm"></i>
                                    </span>
                                    <input type="password" id="pwdNew" name="password"
                                           class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                                  text-sm text-[#0a1929] font-medium
                                                  focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                                  transition-all @error('password') border-red-300 @enderror">
                                </div>
                                @error('password') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Confirm Password</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <i class="bi bi-key text-slate-400 text-sm"></i>
                                    </span>
                                    <input type="password" id="pwdConfirm" name="password_confirmation"
                                           class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                                  text-sm text-[#0a1929] font-medium
                                                  focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                                  transition-all">
                                </div>
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="showPwd" class="w-4 h-4 accent-[#102a43]">
                            <span class="text-xs text-slate-500 font-medium">Show passwords</span>
                        </label>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        <a href="{{ route('admin.users_index') }}"
                           class="text-sm text-slate-500 font-medium hover:text-[#102a43] transition-colors">
                            ← Back to Users
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#102a43] hover:bg-[#0a1929]
                                       text-white text-sm font-bold rounded-xl transition-all
                                       focus:outline-none focus:ring-4 focus:ring-[#102a43]/30 active:scale-[0.99]">
                            <i class="bi bi-check-circle-fill text-[#0ea5e9]"></i>
                            Save Changes
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
document.getElementById('showPwd').addEventListener('change', function() {
    const t = this.checked ? 'text' : 'password';
    document.getElementById('pwdNew').type = t;
    document.getElementById('pwdConfirm').type = t;
});
</script>
@endpush