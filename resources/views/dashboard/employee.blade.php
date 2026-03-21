@extends('layouts.app')

@section('title', 'Employee Dashboard - TeknoHub')

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-tasks fa-2x text-primary mb-2"></i>
                <h3 class="card-title text-main">{{ $activeRepairs }}</h3>
                <p class="card-text text-main">Active Repairs</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h3 class="card-title text-main">{{ $completedRepairsByEmployee }}</h3>
                <p class="card-text text-main">Completed Repairs</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-users fa-2x text-info mb-2"></i>
                <h3 class="card-title text-main">{{ $managedCustomersCount }}</h3>
                <p class="card-text text-main">Managed Customers</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header text-main">
        <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Your Assigned Service Requests</h5>
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
                        <th class="text-main">Date Assigned</th>
                        <th class="text-main">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignedRepairs as $request)
                    <tr>
                        <td class="text-main">{{ $request->service_id }}</td>
                        <td class="text-main">{{ $request->customer->user->full_name }}</td>
                        <td class="text-main">{{ $request->device_type }}</td>
                        <td>
                            <span class="badge bg-{{ $request->status === 'completed' ? 'success' : ($request->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                        </td>
                        <td class="text-main">{{ $request->updated_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('service-requests.show', $request) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('service-requests.edit', $request) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i> Update
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-main">You have no assigned service requests.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection