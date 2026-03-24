@extends('layouts.app')

@section('title', 'Admin Dashboard - TeknoHub')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-wrench fa-2x text-primary mb-2"></i>
                <h3 class="card-title text-main">{{ $stats->totalRequests }}</h3>
                <p class="card-text text-main">Total Requests</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                <h3 class="card-title text-main">{{ $stats->pendingRepairs }}</h3>
                <p class="card-text text-main">Pending Repairs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h3 class="card-title text-main">{{ $stats->completedRepairs }}</h3>
                <p class="card-text text-main">Completed Repairs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                <h3 class="card-title text-main">₱{{ number_format($totalRevenue, 2) }}</h3>
                <p class="card-text text-main">Total Revenue</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-main d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Recent Service Requests
                </h5>
                <a href="{{ route('service-requests.index') }}" class="btn btn-outline-secondary btn-sm">
                    View All
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-main">ID</th>
                                <th class="text-main">Customer</th>
                                <th class="text-main">Device</th>
                                <th class="text-main">Status</th>
                                <th class="text-main">Assigned To</th>
                                <th class="text-main">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRequests as $request)
                            <tr>
                                <td class="text-main">{{ $request->service_id }}</td>
                                <td class="text-main">{{ $request->customer->user->full_name }}</td>
                                <td class="text-main">{{ $request->device_type }}</td>
                                <td>
                                    <span class="badge bg-{{ $request->status === 'completed' ? 'success' : ($request->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td class="text-main">{{ $request->employee ? $request->employee->user->full_name : 'Unassigned' }}</td>
                                <td class="text-main">{{ $request->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-main">No service requests found</td>
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
                <h5 class="mb-0 text-main">
                    <i class="fas fa-users me-2"></i>System Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-main">Total Users</span>
                        <strong class="text-main">{{ $userStats->totalUsers }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-main">Employees</span>
                        <strong class="text-main">{{ $userStats->totalEmployees }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-main">Customers</span>
                        <strong class="text-main">{{ $userStats->totalCustomers }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-main">Pending Approvals</span>
                        <strong class="text-main">{{ $userStats->pendingEmployeeApprovals }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0 text-main">
                    <i class="fas fa-link me-2"></i>Quick Links
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('inventory.create') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-plus me-2"></i>Add Inventory Item
                    </a>
                    <a href="{{ route('queue.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-list me-2"></i>View Queue
                    </a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-success">
                        <i class="fas fa-boxes me-2"></i>Manage Inventory
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