@extends('layouts.app')

@section('title', 'Access Denied | Smart VMS')

@section('content')
<style>
    /* Specific styles for the error page to ensure it centers correctly */
    .error-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .error-card { 
        max-width: 500px; 
        border: none; 
        border-radius: 15px; 
        text-align: center; 
        padding: 40px; 
    }
    .icon-box { 
        font-size: 80px; 
        color: #dc3545; 
        margin-bottom: 20px; 
    }
</style>

<div class="error-container">
    <div class="card error-card shadow-lg">
        <div class="card-body">
            <div class="icon-box">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            
            <h1 class="fw-bold text-dark display-4">403</h1>
            <h3 class="mb-3 text-secondary">Access Denied</h3>
            
            <p class="text-muted mb-4">
                Hello, <strong>{{ auth()->user()->name ?? 'Guest' }}</strong>.<br>
                Your current role (<strong>{{ ucfirst(auth()->user()->role ?? 'Visitor') }}</strong>) 
                does not have the permissions required to access this secure section.
            </p>

            <div class="d-grid gap-2">
                {{-- Returns the user to their specific dashboard (Admin, Guard, or Host) --}}
                <a href="{{ route('dashboard') }}" class="btn btn-dark py-2 rounded-pill shadow-sm">
                    <i class="bi bi-house-door me-2"></i> Return to My Dashboard
                </a>

                {{-- Option to log out and sign in with a different role --}}
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-link text-decoration-none text-muted small">
                        <i class="bi bi-person-x me-1"></i> Switch to a different account
                    </button>
                </form>
            </div>
        </div>
        
        <div class="card-footer bg-transparent border-0 text-muted small pt-0">
            <hr class="my-3 opacity-25">
            &copy; {{ date('Y') }} Smart VMS Security System | Incident Logged
        </div>
    </div>
</div>
@endsection