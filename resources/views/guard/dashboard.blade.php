@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    
    <div class="row align-items-center mb-4 bg-white p-3 shadow-sm rounded">
        <div class="col-md-4">
            <h3 class="fw-bold mb-0">Gate Control</h3>
        </div>
        <div class="col-md-4 text-center">
            <div class="badge bg-dark px-4 py-2 fs-6">
                <i class="bi bi-people-fill me-2"></i> Currently Inside: {{ $inside->count() }}
            </div>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('guard.register') }}" class="btn btn-primary fw-bold">
                <i class="bi bi-person-plus-fill me-1"></i> Manual Registration
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <form action="{{ route('guard.dashboard') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Search by Name or ID Number..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-secondary">Search</button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white fw-bold">EXPECTED TODAY (APPROVED)</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($expected as $v)
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $v->name }}</h6>
                                <small class="text-muted">Host: {{ $v->host_name }} | ID: {{ $v->id_number ?? 'N/A' }}</small>
                            </div>
                            <form action="{{ route('guard.checkin', $v->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-success btn-sm px-3">Confirm Entry</button>
                            </form>
                        </li>
                        @empty
                        <li class="list-group-item text-center py-4 text-muted">No one expected at the moment.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white fw-bold">CURRENTLY INSIDE</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($inside as $v)
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $v->name }}</h6>
                                <small class="text-muted">Entered: {{ $v->checked_in_at->format('H:i') }}</small>
                            </div>
                            <form action="{{ route('guard.checkout', $v->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-danger btn-sm px-3">Check Out</button>
                            </form>
                        </li>
                        @empty
                        <li class="list-group-item text-center py-4 text-muted">No visitors currently inside.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection