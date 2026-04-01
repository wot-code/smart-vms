@extends('layouts.app')

@section('content')

{{-- ===========================================================
     SMART VMS — Login Page
     Filament-inspired: split layout, navy sidebar, clean form
     =========================================================== --}}

{{-- Page-scoped styles: reset body bg, hide portal chrome --}}
<style>
    html, body { background: #f0f4f8 !important; margin: 0 !important; }
    #sidebar-wrapper, nav.navbar { display: none !important; }
    main { padding: 0 !important; background: transparent !important; }
</style>

<div class="min-h-screen flex">

    {{-- ─── LEFT PANEL (navy brand column) ─── --}}
    <div class="hidden lg:flex lg:w-[420px] xl:w-[480px] flex-shrink-0
                bg-[#102a43] flex-col justify-between p-10">

        {{-- Brand --}}
        <div>
            <div class="flex items-center gap-3 mb-12">
                <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center">
                    <i class="bi bi-shield-lock-fill text-[#0ea5e9] text-base"></i>
                </div>
                <span class="font-bold text-white text-base tracking-tight">Smart VMS</span>
            </div>

            <h2 class="text-3xl font-extrabold text-white leading-tight mb-4">
                Secure access<br>for your team.
            </h2>
            <p class="text-white/50 text-sm leading-relaxed">
                Sign in to manage visitor approvals, security logs, and real-time analytics.
            </p>
        </div>

        {{-- Feature list --}}
        <div class="space-y-4">
            @foreach([
                ['bi-person-check-fill', 'Visitor Approval'],
                ['bi-bar-chart-fill',   'Live Analytics'],
                ['bi-shield-check',     'Audit Trail'],
                ['bi-bell-fill',        'SMS Alerts'],
            ] as [$icon, $label])
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                    <i class="bi {{ $icon }} text-[#0ea5e9] text-xs"></i>
                </div>
                <span class="text-sm font-medium text-white/70">{{ $label }}</span>
            </div>
            @endforeach
        </div>

        <p class="text-white/25 text-xs">© 2026 Smart VMS Kenya</p>
    </div>

    {{-- ─── RIGHT PANEL (form area) ─── --}}
    <div class="flex-1 flex flex-col items-center justify-center px-6 py-12 bg-[#f0f4f8]">

        {{-- Form card --}}
        <div class="w-full max-w-md">

            {{-- Mobile-only brand --}}
            <div class="flex items-center gap-2 mb-8 lg:hidden">
                <div class="w-8 h-8 rounded-lg bg-[#102a43] flex items-center justify-center">
                    <i class="bi bi-shield-lock-fill text-[#0ea5e9] text-xs"></i>
                </div>
                <span class="font-bold text-[#102a43] text-sm">Smart VMS</span>
            </div>

            {{-- Heading --}}
            <div class="mb-8">
                <h1 class="text-2xl font-extrabold text-[#0a1929] mb-1">Sign in</h1>
                <p class="text-sm text-slate-500 font-medium">Enter your credentials to access your portal.</p>
            </div>

            {{-- Error alert --}}
            @if($errors->any())
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <i class="bi bi-exclamation-circle-fill text-red-500 text-base flex-shrink-0 mt-0.5"></i>
                <p class="text-sm text-red-700 font-medium">{{ $errors->first() }}</p>
            </div>
            @endif

            {{-- Form --}}
            <form action="{{ url('/login') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email"
                           class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="bi bi-envelope text-slate-400 text-sm"></i>
                        </span>
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="you@institution.co.ke"
                               required autofocus
                               class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                      text-sm text-[#0a1929] font-medium placeholder-slate-400
                                      focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                      transition-all @error('email') border-red-300 @enderror">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password"
                           class="block text-xs font-bold text-[#102a43] uppercase tracking-wider mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="bi bi-key text-slate-400 text-sm"></i>
                        </span>
                        <input type="password" id="password" name="password"
                               placeholder="••••••••"
                               required
                               class="w-full pl-10 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl
                                      text-sm text-[#0a1929] font-medium placeholder-slate-400
                                      focus:outline-none focus:border-[#102a43] focus:ring-4 focus:ring-[#102a43]/10
                                      transition-all">
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember"
                           class="w-4 h-4 rounded border-slate-300 text-[#102a43] accent-[#102a43]">
                    <label for="remember" class="text-sm text-slate-600 font-medium select-none">
                        Keep me signed in
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2
                               bg-[#102a43] hover:bg-[#0a1929] text-white
                               font-bold text-sm rounded-xl py-3.5
                               transition-all duration-150 focus:outline-none
                               focus:ring-4 focus:ring-[#102a43]/30 active:scale-[0.99]">
                    Sign In
                    <i class="bi bi-arrow-right text-sm"></i>
                </button>
            </form>

            {{-- Back link --}}
            <div class="mt-6 text-center">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-1.5 text-sm text-slate-500
                          font-medium hover:text-[#102a43] transition-colors">
                    <i class="bi bi-house-door text-sm"></i>
                    Back to Welcome Page
                </a>
            </div>

        </div>
    </div>

</div>

@endsection