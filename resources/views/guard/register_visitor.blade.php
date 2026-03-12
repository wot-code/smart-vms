@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white p-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Assisted Entry Registration</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4 small">Use this form to register visitors who cannot use the mobile self-service (Elderly, Minors, or VIPs).</p>
                    
                    <form action="{{ route('guard.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-uppercase small">Visitor's Full Name</label>
                            <input type="text" name="full_name" class="form-control form-control-lg" placeholder="Enter Full Name" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-uppercase small">ID / Passport Number</label>
                                <input type="text" name="id_number" class="form-control" placeholder="Optional for minors">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-uppercase small">Phone (If available)</label>
                                <input type="text" name="phone" class="form-control" placeholder="e.g. 0712345678">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-uppercase small">Person to Visit (Host)</label>
                            <select name="host_name" class="form-select" required>
                                <option value="" selected disabled>Select Host...</option>
                                @foreach($hosts as $host)
                                    <option value="{{ $host->name }}">{{ $host->name }} ({{ $host->department ?? 'Staff' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-uppercase small">Purpose of Visit</label>
                            <textarea name="purpose" rows="2" class="form-control" placeholder="Reason for entry..." required></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-pill">
                                REGISTER & CONFIRM ENTRY
                            </button>
                            <a href="{{ route('guard.dashboard') }}" class="btn btn-light rounded-pill">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection