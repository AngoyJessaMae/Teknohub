@extends('layouts.app')

@section('title', 'Service Requests - TeknoHub')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-wrench me-2"></i>Service Requests</h2>
    </div>
    <div class="col-md-6 text-end">
        @if(auth()->user()->role !== 'employee')
        <a href="{{ route('service-requests.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Request
        </a>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Device</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr>
                        <td>{{ $request->service_id }}</td>
                        <td>{{ $request->customer->user->full_name }}</td>
                        <td>{{ $request->device_type }}</td>
                        <td>{{ Str::limit($request->device_description, 50) }}</td>
                        <td>
                            <span class="badge bg-{{ $request->status === 'completed' ? 'success' : ($request->status === 'in_progress' ? 'warning' : ($request->status === 'cancelled' ? 'danger' : 'secondary')) }}">
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                        </td>
                        <td>{{ $request->employee ? $request->employee->user->full_name : 'Unassigned' }}</td>
                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('service-requests.show', $request) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'employee' && $request->employee_id === auth()->user()->employee->employee_id))
                                <a href="{{ route('service-requests.edit', $request) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No service requests found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection