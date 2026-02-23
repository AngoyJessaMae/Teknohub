@extends('layouts.app')

@section('title', 'Edit Service Request #' . $serviceRequest->service_id . ' - TeknoHub')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-main d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Service Request #{{ $serviceRequest->service_id }}
                </h5>
                <a href="{{ route('service-requests.show', $serviceRequest) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('service-requests.update', $serviceRequest) }}">
                    @csrf
                    @method('PUT')

                    @if(auth()->user()->role === 'admin')
                    <div class="mb-3">
                        <label for="employee_id" class="form-label text-main">Assign Employee</label>
                        <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id">
                            <option value="">Unassigned</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->employee_id }}"
                                {{ old('employee_id', $serviceRequest->employee_id) == $employee->employee_id ? 'selected' : '' }}>
                                {{ $employee->user->full_name }} - {{ $employee->department_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="status" class="form-label text-main">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="pending" {{ old('status', $serviceRequest->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status', $serviceRequest->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $serviceRequest->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $serviceRequest->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($serviceRequest->status === 'completed' || old('status') === 'completed')
                    <div class="mb-3">
                        <label for="date_completed" class="form-label">Completion Date</label>
                        <input type="datetime-local" class="form-control @error('date_completed') is-invalid @enderror"
                            id="date_completed" name="date_completed"
                            value="{{ old('date_completed', $serviceRequest->date_completed ? \Carbon\Carbon::parse($serviceRequest->date_completed)->format('Y-m-d\\TH:i') : '') }}">
                        @error('date_completed')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('service-requests.show', $serviceRequest) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-main">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Request Details
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Customer:</strong> {{ $serviceRequest->customer->user->full_name }}</p>
                <p class="mb-2"><strong>Device:</strong> {{ $serviceRequest->device_type }}</p>
                <p class="mb-2"><strong>Created:</strong> {{ $serviceRequest->created_at->format('M d, Y H:i') }}</p>
                @if($serviceRequest->employee)
                <p class="mb-2"><strong>Assigned:</strong> {{ $serviceRequest->employee->user->full_name }}</p>
                @endif
            </div>
        </div>

        <div class="card mt-3 text-main">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Status Guidelines
                </h5>
            </div>
            <div class="card-body">
                <ul class="text-muted small">
                    <li><strong>Pending:</strong> Request received, waiting for assignment</li>
                    <li><strong>In Progress:</strong> Currently being worked on</li>
                    <li><strong>Completed:</strong> Repair finished, ready for pickup</li>
                    <li><strong>Cancelled:</strong> Request cancelled</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection