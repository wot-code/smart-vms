<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $viewTitle ?? config('app.name', 'Smart VMS') }}</title>

    {{-- Inter font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons (used across all views) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    {{-- Bootstrap CSS is no longer needed in this layout (portal pages use layouts.portal) --}}

    {{-- Compiled Tailwind CSS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        .fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

        /* Portal navbar/sidebar vars — used only when Bootstrap is active */
        :root { --vms-primary: #102a43; --vms-sidebar: #0a1929; }
    </style>

    @stack('styles')
</head>
<body class="antialiased">

    @if(Auth::check() && !Request::is('/') && !Request::is('login'))
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm" style="background-color: var(--vms-primary);">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <i class="bi bi-shield-lock-fill fs-3 me-2" style="color: #0ea5e9;"></i>
                <span class="fw-semibold">Smart VMS</span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-white-50 small d-none d-md-inline">{{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm rounded-pill px-3">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    @endif

    <main class="fade-in">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
