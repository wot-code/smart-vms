<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? ($viewTitle ?? 'Smart VMS') }} — Smart VMS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Inter', sans-serif; }
        .fi-sidebar-item-active { background: rgba(14,165,233,0.1) !important; color: #0ea5e9 !important; }
        .fi-sidebar-item-active i { color: #0ea5e9 !important; }
    </style>

    @stack('styles')
</head>
<body class="bg-[#f0f4f8] antialiased" x-data="{ sidebarOpen: false }">

<div class="flex h-screen overflow-hidden">

    {{-- ═══════════════════════════════════════
         MOBILE OVERLAY
    ═══════════════════════════════════════ --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-20 bg-black/50 lg:hidden"
         style="display: none;"></div>

    {{-- ═══════════════════════════════════════
         SIDEBAR
    ═══════════════════════════════════════ --}}
    <aside class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col bg-[#0a1929] transition-transform duration-200 lg:static lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        {{-- Brand --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/5">
            <div class="w-8 h-8 rounded-lg bg-[#102a43] flex items-center justify-center flex-shrink-0 border border-white/10">
                <i class="bi bi-shield-lock-fill text-[#0ea5e9] text-sm"></i>
            </div>
            <div>
                <p class="text-white text-sm font-bold leading-tight">Smart VMS</p>
                <p class="text-[#0ea5e9] text-[0.65rem] font-bold uppercase tracking-widest mt-0.5">
                    @if(Auth::user()->role === 'admin') Control Panel @elseif(Auth::user()->role === 'guard') Security Desk @else Host Portal @endif
                </p>
            </div>
            <button @click="sidebarOpen = false" class="ml-auto text-white/40 hover:text-white lg:hidden">
                <i class="bi bi-x-lg text-base"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">

            @if(Auth::user()->role === 'admin')
                {{-- ADMIN NAVIGATION --}}
                <p class="text-[0.65rem] font-bold text-white/25 uppercase tracking-widest px-3 pt-2 pb-1.5">Overview</p>
                
                <a href="{{ route('admin.analytics') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/60
                           hover:bg-white/5 hover:text-white transition-all
                           {{ request()->routeIs('admin.analytics') ? 'fi-sidebar-item-active bg-white/5' : '' }}">
                    <i class="bi bi-pie-chart-fill text-base {{ request()->routeIs('admin.analytics') ? 'text-[#0ea5e9]' : 'text-white/40 group-hover:text-white/70' }}"></i>
                    Analytics
                </a>
                
                <a href="{{ route('dashboard') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/60
                           hover:bg-white/5 hover:text-white transition-all
                           {{ request()->routeIs('dashboard') ? 'fi-sidebar-item-active bg-white/5' : '' }}">
                    <i class="bi bi-activity text-base {{ request()->routeIs('dashboard') ? 'text-[#0ea5e9]' : 'text-white/40 group-hover:text-white/70' }}"></i>
                    Live Monitor
                </a>

                <p class="text-[0.65rem] font-bold text-white/25 uppercase tracking-widest px-3 pt-4 pb-1.5">Management</p>
                
                <a href="{{ route('admin.users_index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/60
                           hover:bg-white/5 hover:text-white transition-all
                           {{ request()->routeIs('admin.users_index') ? 'fi-sidebar-item-active bg-white/5' : '' }}">
                    <i class="bi bi-people-fill text-base {{ request()->routeIs('admin.users_index') ? 'text-[#0ea5e9]' : 'text-white/40 group-hover:text-white/70' }}"></i>
                    System Users
                </a>
                
                <p class="text-[0.65rem] font-bold text-white/25 uppercase tracking-widest px-3 pt-4 pb-1.5">Security</p>
                
                <a href="{{ route('admin.audit_logs') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/60
                           hover:bg-white/5 hover:text-white transition-all
                           {{ request()->routeIs('admin.audit_logs') ? 'fi-sidebar-item-active bg-white/5' : '' }}">
                    <i class="bi bi-shield-lock-fill text-base {{ request()->routeIs('admin.audit_logs') ? 'text-[#0ea5e9]' : 'text-white/40 group-hover:text-white/70' }}"></i>
                    Audit Trail
                </a>

            @elseif(Auth::user()->role === 'guard')
                {{-- GUARD NAVIGATION --}}
                <p class="text-[0.65rem] font-bold text-white/25 uppercase tracking-widest px-3 pt-2 pb-1.5">Security Desk</p>
                
                <a href="{{ route('guard.dashboard') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/60
                           hover:bg-white/5 hover:text-white transition-all
                           {{ request()->routeIs('guard.dashboard') ? 'fi-sidebar-item-active bg-white/5' : '' }}">
                    <i class="bi bi-door-open-fill text-base {{ request()->routeIs('guard.dashboard') ? 'text-[#0ea5e9]' : 'text-white/40 group-hover:text-white/70' }}"></i>
                    Gate Operations
                </a>
                
                <a href="{{ route('guard.register') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/60
                           hover:bg-white/5 hover:text-white transition-all
                           {{ request()->routeIs('guard.register') ? 'fi-sidebar-item-active bg-white/5' : '' }}">
                    <i class="bi bi-person-plus-fill text-base {{ request()->routeIs('guard.register') ? 'text-[#0ea5e9]' : 'text-white/40 group-hover:text-white/70' }}"></i>
                    Manual Entry
                </a>

            @elseif(Auth::user()->role === 'host')
                {{-- HOST NAVIGATION --}}
                <p class="text-[0.65rem] font-bold text-white/25 uppercase tracking-widest px-3 pt-2 pb-1.5">My Portal</p>
                
                <a href="{{ route('dashboard') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/60
                           hover:bg-white/5 hover:text-white transition-all
                           {{ request()->routeIs('dashboard') ? 'fi-sidebar-item-active bg-white/5' : '' }}">
                    <i class="bi bi-people-fill text-base {{ request()->routeIs('dashboard') ? 'text-[#0ea5e9]' : 'text-white/40 group-hover:text-white/70' }}"></i>
                    My Visitors
                </a>

            @endif

        </nav>

        {{-- User Footer --}}
        <div class="border-t border-white/5 px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-[#102a43] border border-white/10 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-[#0ea5e9]">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[0.65rem] text-white/30 capitalize">{{ Auth::user()->role }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout"
                            class="w-7 h-7 rounded-lg bg-white/5 hover:bg-red-500/20 flex items-center justify-center
                                   text-white/30 hover:text-red-400 transition-all">
                        <i class="bi bi-box-arrow-right text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ═══════════════════════════════════════
         MAIN CONTENT AREA
    ═══════════════════════════════════════ --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        {{-- Top bar --}}
        <header class="flex items-center gap-4 px-6 py-3.5 bg-white border-b border-slate-200 flex-shrink-0">
            {{-- Mobile menu toggle --}}
            <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 transition-colors">
                <i class="bi bi-list text-xl"></i>
            </button>

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-sm text-slate-500 min-w-0">
                <a href="{{ route('admin.analytics') }}" class="hover:text-[#102a43] font-medium transition-colors flex-shrink-0">Smart VMS</a>
                <i class="bi bi-chevron-right text-xs text-slate-300"></i>
                <span class="text-[#102a43] font-semibold truncate">{{ $pageTitle ?? ($viewTitle ?? 'Dashboard') }}</span>
            </div>

            <div class="ml-auto flex items-center gap-3">
                <span class="hidden sm:flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
                    System Online
                </span>
                <a href="{{ url('/') }}"
                   class="flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-[#102a43]
                          border border-slate-200 rounded-lg px-3 py-1.5 transition-all hover:border-slate-300">
                    <i class="bi bi-house text-sm"></i>
                    <span class="hidden sm:inline">Portal Home</span>
                </a>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto">
            {{-- Flash messages --}}
            @if(session('success'))
            <div class="mx-6 mt-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
                <i class="bi bi-check-circle-fill text-emerald-500 flex-shrink-0"></i>
                <p class="text-sm text-emerald-700 font-medium">{{ session('success') }}</p>
            </div>
            @endif
            @if(session('error'))
            <div class="mx-6 mt-4 flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                <i class="bi bi-exclamation-circle-fill text-red-500 flex-shrink-0"></i>
                <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@livewireScripts
@stack('scripts')
</body>
</html>
