@extends('layouts.app')

@section('title', 'Service Request #' . $serviceRequest->service_id . ' - TeknoHub')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center text-main">
                <h5 class="mb-0">
                    <i class="fas fa-wrench me-2"></i>Service Request #{{ $serviceRequest->service_id }}
                </h5>
                <div class="btn-group">
                    <a href="{{ route('service-requests.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                    @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'employee' && $serviceRequest->employee_id === auth()->user()->employee->employee_id))
                    <a href="{{ route('service-requests.edit', $serviceRequest) }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body text-main">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Customer Information</h6>
                        <p class="mb-1"><strong>Name:</strong> {{ $serviceRequest->customer->user->full_name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $serviceRequest->customer->user->email }}</p>
                        <p class="mb-0"><strong>Contact:</strong> {{ $serviceRequest->customer->user->contact_number }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Device Information</h6>
                        <p class="mb-1"><strong>Type:</strong> {{ $serviceRequest->device_type }}</p>
                        <p class="mb-1"><strong>Status:</strong>
                            <span class="badge bg-{{ $serviceRequest->status === 'completed' ? 'success' : ($serviceRequest->status === 'in_progress' ? 'warning' : ($serviceRequest->status === 'cancelled' ? 'danger' : 'secondary')) }}">
                                {{ ucfirst(str_replace('_', ' ', $serviceRequest->status)) }}
                            </span>
                        </p>
                        <p class="mb-0"><strong>Created:</strong> {{ $serviceRequest->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <h6>Device Description</h6>
                    <p class="text-main">{{ $serviceRequest->device_description }}</p>
                </div>

                @if($serviceRequest->problem_description)
                <div class="mb-3">
                    <h6>Problem Description</h6>
                    <p class="text-main">{{ $serviceRequest->problem_description }}</p>
                </div>
                @endif

                @if($serviceRequest->date_received)
                <div class="mb-3">
                    <h6>Date Received</h6>
                    <p class="text-main">{{ \Carbon\Carbon::parse($serviceRequest->date_received)->format('M d, Y H:i') }}</p>
                </div>
                @endif

                @if($serviceRequest->appointment_request)
                <div class="mb-3">
                    <h6>Requested Appointment</h6>
                    <p class="text-main">{{ \Carbon\Carbon::parse($serviceRequest->appointment_request)->format('M d, Y H:i') }}</p>
                </div>
                @endif

                @if($serviceRequest->employee)
                <div class="mb-3">
                    <h6>Assigned Employee</h6>
                    <p class="mb-1"><strong>Name:</strong> {{ $serviceRequest->employee->user->full_name }}</p>
                    <p class="mb-1"><strong>Department:</strong> {{ $serviceRequest->employee->department_name }}</p>
                    <p class="mb-0"><strong>Title:</strong> {{ $serviceRequest->employee->job_title }}</p>
                </div>
                @endif

                @if($serviceRequest->date_completed)
                <div class="mb-3">
                    <h6>Completion Details</h6>
                    <p class="mb-0"><strong>Completed:</strong> {{ \Carbon\Carbon::parse($serviceRequest->date_completed)->format('M d, Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>

        @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'employee' && $serviceRequest->employee_id === auth()->user()->employee->employee_id))
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-boxes me-2"></i>Parts Used
                </h5>
            </div>
            <div class="card-body">
                @if($serviceRequest->purchases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-dark table-sm">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceRequest->purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->item->item_name }}</td>
                                <td>{{ $purchase->item->category }}</td>
                                <td>{{ $purchase->quantity }}</td>
                                <td>₱{{ number_format($purchase->item->price, 2) }}</td>
                                <td>₱{{ number_format($purchase->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted">No parts have been added to this service request yet.</p>
                @endif

@if(auth()->user()->role === 'admin' || auth()->user()->role === 'Employee')
                <div class="mt-3">
                    <form method="POST" action="{{ route('inventory.add-to-service', $serviceRequest->service_id) }}" class="row g-2">
                        @csrf
                        <div class="col-auto">
                            <select name="item_id" class="form-select form-select-sm" required>
                                <option value="">Select Item</option>
                                @foreach(\App\Models\Item::where('stock_quantity', '>', 0)->get() as $item)
                                <option value="{{ $item->item_id }}">{{ $item->item_name }} - ₱{{ $item->price }} ({{ $item->stock_quantity }} in stock)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <input type="number" name="quantity" class="form-control form-control-sm" placeholder="Qty" min="1" required>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm">Add Item</button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        @if($serviceRequest->status !== 'cancelled')
        <div class="card text-main">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-peso-sign me-2"></i>Billing Information
                </h5>
                @if(auth()->user()->role === 'Employee' || auth()->user()->role === 'admin')
                <a href="{{ route('service-requests.billing.create', $serviceRequest) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-file-invoice me-1"></i>Generate Billing
                </a>
                @endif
            </div>
            <div class="card-body">
                @if($serviceRequest->billing)
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Labor Fee:</span>
                        <strong>₱{{ number_format($serviceRequest->billing->labor_fee, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Parts Fee:</span>
                        <strong>₱{{ number_format($serviceRequest->billing->parts_fee, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Total Amount:</span>
                        <strong>₱{{ number_format($serviceRequest->billing->total_amount, 2) }}</strong>
                    </div>
                </div>

                @if($serviceRequest->billing->warranty)
                <div class="mb-2">
                    <strong>Warranty:</strong>
                    <span>{{ $serviceRequest->billing->warranty }}</span>
                </div>
                @endif

@if($serviceRequest->billing->employee_id && $serviceRequest->billing->employee)
                <div class="mb-2">
                    <strong>Billing Officer:</strong>
                    <span>{{ $serviceRequest->billing->employee->employee_id }} - {{ $serviceRequest->billing->employee->user->full_name }}</span>
                </div>
                @endif

                @if($serviceRequest->billing->date_billed)
                <div class="mb-2">
                    <strong>Date Billed:</strong>
                    <span>{{ \Carbon\Carbon::parse($serviceRequest->billing->date_billed)->format('M d, Y') }}</span>
                </div>
                @endif

                @if($serviceRequest->billing->payment_mode)
                <div class="mb-2">
                    <strong>Mode of Payment:</strong>
                    <span>{{ $serviceRequest->billing->payment_mode }}</span>
                </div>
                @endif

                <div class="mb-3">
                    <strong>Payment Status:</strong>
                    <span class="badge bg-{{ $serviceRequest->billing->payment_status === 'Paid' ? 'success' : ($serviceRequest->billing->payment_status === 'Pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($serviceRequest->billing->payment_status) }}
                    </span>
                </div>

                @if($serviceRequest->billing->payment_date)
                <div class="mb-3">
                    <strong>Payment Date:</strong>
                    <span>{{ \Carbon\Carbon::parse($serviceRequest->billing->payment_date)->format('M d, Y') }}</span>
                </div>
                @endif

                @if(auth()->user()->role === 'Employee' || auth()->user()->role === 'admin')
                <a href="{{ route('service-requests.billing.create', $serviceRequest) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-edit me-1"></i>Edit Billing
                </a>
                @endif
                @else
                <p class="text-muted">No billing information available.</p>
                @if(auth()->user()->role === 'Employee' || auth()->user()->role === 'admin')
                <a href="{{ route('service-requests.billing.create', $serviceRequest) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Create Billing
                </a>
                @endif
                @endif
            </div>
        </div>
        @else
        <div class="card text-main">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Cancelled Request
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">This service request has been cancelled.</p>
                @if($serviceRequest->billing)
                <p class="text-danger">Note: This request still has billing information that should be removed.</p>
                <form method="POST" action="{{ route('billing.delete-for-cancelled', $serviceRequest->billing) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this billing?')">
                        <i class="fas fa-trash me-1"></i>Delete Billing
                    </button>
                </form>
                @else
                <p class="text-success">No billing record exists for this cancelled request.</p>
                @endif
        </div>
            </div>
        @endif

        @if($serviceRequest->queue)
        <div class="card mt-3 text-main">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list-ol me-2"></i>Queue Information
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Position:</strong> #{{ $serviceRequest->queue->queue_position }}</p>
                @if($serviceRequest->queue->queue_number)
                <p class="mb-1"><strong>Queue Number:</strong> {{ $serviceRequest->queue->queue_number }}</p>
                @endif
                @if($serviceRequest->queue->priority_level)
                <p class="mb-1"><strong>Priority Level:</strong> 
                    <span class="badge bg-{{ $serviceRequest->queue->priority_level === 'Urgent' ? 'danger' : ($serviceRequest->queue->priority_level === 'High' ? 'warning' : 'secondary') }}">
                        {{ $serviceRequest->queue->priority_level }}
                    </span>
                </p>
                @endif
                <p class="mb-0"><strong>Status:</strong>
                    <span class="badge bg-{{ $serviceRequest->queue->queue_status === 'completed' ? 'success' : ($serviceRequest->queue->queue_status === 'in_progress' ? 'warning' : 'secondary') }}">
                        {{ ucfirst(str_replace('_', ' ', $serviceRequest->queue->queue_status ?? $serviceRequest->queue->status)) }}
                    </span>
                </p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection