<div class="vms-sidebar shadow d-flex flex-column">
    <div class="sidebar-header px-4 py-4 border-bottom border-secondary">
        <div class="d-flex align-items-center">
            <i class="bi bi-shield-check text-info fs-4 me-2"></i>
            <h6 class="text-light text-uppercase small fw-bold mb-0" style="letter-spacing: 1px;">Control Panel</h6>
        </div>
    </div>
    
    <div class="list-group list-group-flush flex-grow-1">
        {{-- SECTION: GUEST LINKS --}}
        @guest
            <div class="px-4 py-2 mt-3 text-uppercase tiny-font text-muted fw-bold">Public Access</div>
            <a href="{{ url('/') }}" class="list-group-item list-group-item-action bg-transparent text-light border-0 py-3">
                <i class="bi bi-house me-3 fs-5 text-info"></i> Welcome Page
            </a>
            <a href="{{ route('login') }}" class="list-group-item list-group-item-action bg-transparent text-light border-0 py-3 {{ request()->is('login') ? 'active-link' : '' }}">
                <i class="bi bi-box-arrow-in-right me-3 fs-5 text-info"></i> Staff Login
            </a>
        @endguest

        {{-- SECTION: AUTHENTICATED LINKS --}}
        @auth
            <div class="px-4 py-2 mt-3 text-uppercase tiny-font text-muted fw-bold">Monitoring</div>
            
            <a href="{{ route('dashboard') }}" 
               class="list-group-item list-group-item-action bg-transparent text-light border-0 py-3 {{ request()->routeIs('dashboard') ? 'active-link' : '' }}">
                <i class="bi bi-speedometer2 me-3 fs-5 text-info"></i> System Overview
            </a>

            {{-- GUARD SPECIFIC --}}
            @if(auth()->user()->role === 'guard')
                <a href="{{ route('guard.register') }}" 
                   class="list-group-item list-group-item-action bg-transparent text-light border-0 py-3 {{ request()->routeIs('guard.register') ? 'active-link' : '' }}">
                    <i class="bi bi-person-plus-fill me-3 fs-5 text-info"></i> Assisted Entry
                </a>
            @endif

            {{-- ADMIN SPECIFIC --}}
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.users_index') }}" 
                   class="list-group-item list-group-item-action bg-transparent text-light border-0 py-3 {{ request()->routeIs('admin.users_index') ? 'active-link' : '' }}">
                    <i class="bi bi-people me-3 fs-5 text-info"></i> Manage Hosts
                </a>

                <div class="px-4 py-2 mt-3 text-uppercase tiny-font text-muted fw-bold">Security & Audit</div>

                <a href="{{ route('admin.audit_logs') }}" 
                   class="list-group-item list-group-item-action bg-transparent text-light border-0 py-3 {{ request()->routeIs('admin.audit_logs') ? 'active-link' : '' }}">
                    <i class="bi bi-clipboard-data me-3 fs-5 text-warning"></i> Audit Trail
                </a>

                @if(Route::has('admin.analytics'))
                <a href="{{ route('admin.analytics') }}" 
                   class="list-group-item list-group-item-action bg-transparent text-light border-0 py-3 {{ request()->routeIs('admin.analytics') ? 'active-link' : '' }}">
                    <i class="bi bi-graph-up-arrow me-3 fs-5 text-success"></i> Web Analytics
                </a>
                @endif
            @endif
        @endauth
    </div>

    <div class="sidebar-footer p-4 border-top border-secondary">
        <div class="d-flex align-items-center text-secondary small">
            <i class="bi bi-patch-check-fill me-2 text-info"></i>
            <div>
                <p class="mb-0 fw-bold text-light">VMS v1.0.4</p>
                <span style="font-size: 0.7rem;">Stable Build (Kenya)</span>
            </div>
        </div>
    </div>
</div>

<style>
    .vms-sidebar {
        background-color: #1a252f;
        min-height: 100vh;
        color: #ecf0f1;
        transition: all 0.3s;
    }
    .tiny-font {
        font-size: 0.65rem;
        letter-spacing: 1.2px;
    }
    .list-group-item {
        transition: all 0.2s ease-in-out;
        font-weight: 500;
        color: #bdc3c7 !important;
        font-size: 0.95rem;
        text-decoration: none;
    }
    .list-group-item:hover {
        background-color: #2c3e50 !important;
        color: #3498db !important;
        padding-left: 1.5rem; /* Smoother hover slide */
    }
    .active-link {
        background-color: #2c3e50 !important;
        border-left: 4px solid #3498db !important;
        color: #fff !important;
    }
    .flex-grow-1 {
        flex: 1 0 auto;
    }
</style>