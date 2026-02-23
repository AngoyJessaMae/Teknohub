@extends('layouts.app')

@section('title', 'Employee Dashboard - TeknoHub')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-tasks fa-2x text-primary mb-2"></i>
                <h3 class="card-title text-main">{{ count($assignedRepairs) }}</h3>
                <p class="card-text text-muted">Assigned Repairs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-cogs fa-2x text-warning mb-2"></i>
                <h3 class="card-title text-main">{{ $activeRepairs }}</h3>
                <p class="card-text text-muted">Active Repairs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h3 class="card-title text-main">{{ $completedRepairsByEmployee }}</h3>
                <p class="card-text text-muted">Completed Repairs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-users fa-2x text-info mb-2"></i>
                <h3 class="card-title text-main">{{ $managedCustomersCount }}</h3>
                <p class="card-text text-muted">Managed Customers</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header text-main d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-tasks me-2"></i>My Assigned Requests
                </h5>
                <a href="{{ route('queue.index') }}" class="btn btn-outline-secondary btn-sm">
                    View Queue
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Device</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignedRepairs as $request)
                            <tr>
                                <td>{{ $request->service_id }}</td>
                                <td>{{ $request->customer->user->full_name }}</td>
                                <td>{{ $request->device_type }}</td>
                                <td>
                                    <span class="badge bg-{{ $request->status === 'completed' ? 'success' : ($request->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('service-requests.edit', $request) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No assigned requests</td>
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