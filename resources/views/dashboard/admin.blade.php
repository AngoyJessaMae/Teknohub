@extends('layouts.app')

@section('title', 'Admin Dashboard - TeknoHub')

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
                <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                <h3 class="card-title">${{ number_format($totalRevenue, 2) }}</h3>
                <p class="card-text text-muted">Total Revenue</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Recent Service Requests
                </h5>
                <a href="{{ route('service-requests.index') }}" class="btn btn-outline-primary btn-sm">
                    View All
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Device</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRequests as $request)
                            <tr>
                                <td>{{ $request->service_id }}</td>
                                <td>{{ $request->customer->user->full_name }}</td>
                                <td>{{ $request->device_type }}</td>
                                <td>
                                    <span class="badge bg-{{ $request->status === 'completed' ? 'success' : ($request->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td>{{ $request->employee ? $request->employee->user->full_name : 'Unassigned' }}</td>
                                <td>{{ $request->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No service requests found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>System Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Total Users</span>
                        <strong>{{ $totalUsers }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Employees</span>
                        <strong>{{ $totalEmployees }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Customers</span>
                        <strong>{{ $totalCustomers }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Pending Approvals</span>
                        <strong>{{ $pendingEmployeeApprovals }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-link me-2"></i>Quick Links
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('service-requests.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>New Service Request
                    </a>
                    <a href="{{ route('inventory.create') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-plus me-2"></i>Add Inventory Item
                    </a>
                    <a href="{{ route('queue.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-list me-2"></i>View Queue
                    </a>
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-dark">
                        <i class="fas fa-users-cog me-2"></i>Manage Employees
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection