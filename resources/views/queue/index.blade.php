@extends('layouts.app')

@section('title', 'Service Queue - TeknoHub')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-list-ol me-2"></i>Service Queue</h2>
    </div>
    <div class="col-md-6 text-end">
        <form method="POST" action="{{ route('queue.process-next') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="fas fa-play me-2"></i>Process Next Request
            </button>
        </form>
    </div>
</div>

<div class="row">
    @forelse($queues as $queue)
    <div class="col-md-4 mb-4">
        <div class="card {{ $queue->queue_position === 1 ? 'border-success' : '' }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="badge bg-{{ $queue->queue_position === 1 ? 'success' : 'primary' }} fs-6">
                    Position #{{ $queue->queue_position }}
                </span>
                <span class="badge bg-{{ $queue->status === 'in_progress' ? 'warning' : 'secondary' }}">
                    {{ ucfirst(str_replace('_', ' ', $queue->status)) }}
                </span>
            </div>
            <div class="card-body">
                <h6 class="card-title">Service Request #{{ $queue->serviceRequest->service_id }}</h6>
                <p class="card-text">
                    <strong>Customer:</strong> {{ $queue->serviceRequest->customer->user->full_name }}<br>
                    <strong>Device:</strong> {{ $queue->serviceRequest->device_type }}<br>
                    <strong>Created:</strong> {{ $queue->serviceRequest->created_at->format('M d, Y H:i') }}
                </p>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('service-requests.show', $queue->serviceRequest) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye me-1"></i>View
                    </a>

                    @if(auth()->user()->role === 'employee')
                    <a href="{{ route('service-requests.edit', $queue->serviceRequest) }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Update
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No items in queue</h5>
                <p class="text-muted">All service requests have been processed.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($queues->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Queue Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h4 class="text-primary">{{ $queues->count() }}</h4>
                        <p class="text-muted mb-0">Total in Queue</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-warning">{{ $queues->where('status', 'in_progress')->count() }}</h4>
                        <p class="text-muted mb-0">In Progress</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-success">{{ $queues->where('queue_position', 1)->count() }}</h4>
                        <p class="text-muted mb-0">Next in Line</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-info">{{ $queues->avg('queue_position') ? round($queues->avg('queue_position'), 1) : 0 }}</h4>
                        <p class="text-muted mb-0">Avg Position</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection