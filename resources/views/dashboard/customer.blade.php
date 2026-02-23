@extends('layouts.app')

@section('title', 'Customer Dashboard - TeknoHub')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-wrench fa-2x text-primary mb-2"></i>
                <h3 class="card-title">{{ $totalRequests }}</h3>
                <p class="card-text text-muted">Total Requests</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                <h3 class="card-title">{{ $pendingRepairs }}</h3>
                <p class="card-text text-muted">Pending Repairs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h3 class="card-title">{{ $completedRepairs }}</h3>
                <p class="card-text text-muted">Completed Repairs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-plus fa-2x text-info mb-2"></i>
                <h3 class="card-title">New</h3>
                <p class="card-text text-muted">Service Request</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>My Service Requests
                </h5>
                <a href="{{ route('service-requests.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Request
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Device</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Total Cost</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customerRequests as $request)
                            <tr>
                                <td>{{ $request->service_id }}</td>
                                <td>{{ $request->device_type }}</td>
                                <td>{{ Str::limit($request->device_description, 50) }}</td>
                                <td>
                                    <span class="badge bg-{{ $request->status === 'completed' ? 'success' : ($request->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td>{{ $request->employee ? $request->employee->user->full_name : 'Unassigned' }}</td>
                                <td>${{ number_format($request->billing->total_amount ?? 0, 2) }}</td>
                                <td>
                                    <a href="{{ route('service-requests.show', $request) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No service requests found. Create your first request!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection