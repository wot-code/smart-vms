@extends('layouts.app')

@section('content')

{{-- =====================================================================
     SMART VMS — Welcome / Landing Page
     High-contrast, bold typography, minimalist navy, pure Tailwind v4
     ===================================================================== --}}

<div class="min-h-screen bg-[#f0f4f8] flex flex-col" x-data="{ showForm: false }">

    {{-- ─────────── TOP BAR ─────────── --}}
    <header class="w-full bg-white border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            {{-- Brand --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#102a43] flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-shield-lock-fill text-[#0ea5e9] text-sm"></i>
                </div>
                <span class="font-bold text-[#102a43] text-base tracking-tight">Smart VMS</span>
            </div>

            {{-- Staff login link --}}
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-[#102a43]
                      border border-slate-300 rounded-lg px-4 py-2
                      hover:bg-[#102a43] hover:text-white hover:border-[#102a43]
                      transition-all duration-150">
                <i class="bi bi-person-lock text-sm"></i>
                Staff Login
            </a>
        </div>
    </header>

    {{-- ─────────── MAIN ─────────── --}}
    <main class="flex-1 flex flex-col items-center justify-center px-4 py-16">

        {{-- CHOICE VIEW --}}
        <div x-show="!showForm"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            {{-- Hero text --}}
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full
                            bg-[#102a43] text-white text-xs font-semibold tracking-widest uppercase mb-5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#0ea5e9] inline-block"></span>
                    Kenya Institutional Security
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-[#0a1929] tracking-tight mb-4 leading-tight">
                    Secure Access.<br class="hidden sm:block">
                    <span class="text-[#0ea5e9]">Smart Intelligence.</span>
                </h1>
                <p class="text-slate-600 text-base max-w-sm mx-auto leading-relaxed font-medium">
                    Kenya's most advanced institutional visitor management platform — built for security, compliance, and speed.
                </p>
            </div>

            {{-- Action Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 w-full max-w-2xl">

                {{-- Visitor Check-In Card --}}
                <button @click="showForm = true"
                        class="group bg-white border-2 border-slate-200 rounded-2xl p-7 text-left
                               hover:border-[#102a43] hover:shadow-xl transition-all duration-200
                               focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0ea5e9]">
                    <div class="w-12 h-12 rounded-xl bg-[#102a43] flex items-center justify-center mb-5">
                        <i class="bi bi-person-vcard-fill text-[#0ea5e9] text-xl"></i>
                    </div>
                    <h2 class="text-lg font-bold text-[#0a1929] mb-2">Visitor Check-In</h2>
                    <p class="text-sm text-slate-500 leading-relaxed mb-5">
                        Register your visit, notify your host, and receive your digital pass instantly.
                    </p>
                    <span class="inline-flex items-center gap-2 text-sm font-bold text-[#0ea5e9]
                                 group-hover:gap-3 transition-all">
                        Start Registration
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </button>

                {{-- Staff Portal Card --}}
                <a href="{{ route('login') }}"
                   class="group bg-[#102a43] border-2 border-[#102a43] rounded-2xl p-7
                          hover:bg-[#0a1929] hover:border-[#0a1929] hover:shadow-xl transition-all duration-200
                          focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0ea5e9]">
                    <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center mb-5">
                        <i class="bi bi-grid-3x3-gap-fill text-white text-xl"></i>
                    </div>
                    <h2 class="text-lg font-bold text-white mb-2">Staff Portal</h2>
                    <p class="text-sm text-white/60 leading-relaxed mb-5">
                        Admin, host, and guard login to manage approvals, logs, and analytics.
                    </p>
                    <span class="inline-flex items-center gap-2 text-sm font-bold text-[#38bdf8]
                                 group-hover:gap-3 transition-all">
                        Sign In
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </a>

            </div>

            {{-- Status strip --}}
            <div class="mt-8 flex items-center justify-center gap-4 text-xs text-slate-500 font-medium">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                    System Online
                </span>
                <span class="text-slate-300">|</span>
                <span>SMS Active</span>
                <span class="text-slate-300">|</span>
                <span>Data Protection Compliant</span>
            </div>
        </div>

        {{-- REGISTRATION FORM VIEW --}}
        <div x-show="showForm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             style="display: none;"
             class="w-full max-w-3xl">

            <div class="bg-white border-2 border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                {{-- Card header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-[#1e3a5f] bg-[#102a43]">
                    <div class="flex items-center gap-3">
                        <button @click="showForm = false"
                                class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20
                                       flex items-center justify-center text-white transition-colors">
                            <i class="bi bi-arrow-left text-sm"></i>
                        </button>
                        <div>
                            <h2 class="text-sm font-bold text-white">Visitor Registration</h2>
                            <p class="text-xs text-white/50">Fill in your details below</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                                 bg-[#0ea5e9]/20 text-[#38bdf8]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0ea5e9] inline-block"></span>
                        Self Check-In
                    </span>
                </div>

                {{-- Livewire form --}}
                <div class="p-6 sm:p-8">
                    @livewire('visitor-registration-form')
                </div>
            </div>
        </div>

    </main>

    {{-- ─────────── FOOTER ─────────── --}}
    <footer class="w-full border-t border-slate-200 bg-white">
        <div class="max-w-5xl mx-auto px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-1">
            <p class="text-xs font-medium text-slate-500">© 2026 Smart VMS Kenya. All rights reserved.</p>
            <p class="text-xs text-slate-400">Institutional Security & Safety Standards Compliant.</p>
        </div>
    </footer>

</div>

@endsection