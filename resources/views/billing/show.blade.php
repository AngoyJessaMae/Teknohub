@extends('layouts.app')

@section('title', 'Billing Details - TeknoHub')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-main d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-file-invoice me-2"></i>Billing Details for Service #{{ $billing->service_id }}
                </h5>
                <a href="{{ route('billing.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back to Billing
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-main">Billing Summary</h6>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Labor Fee:
                                <span>₱{{ number_format($billing->labor_fee, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Parts Fee:
                                <span>₱{{ number_format($billing->parts_fee, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                <strong class="text-main">Total Amount:</strong>
                                <strong class="text-main">₱{{ number_format($billing->total_amount, 2) }}</strong>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-main">Payment Status</h6>
                        <div class="d-grid">
                            <span class="badge fs-5 bg-{{ $billing->payment_status === 'paid' ? 'success' : ($billing->payment_status === 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($billing->payment_status) }}
                            </span>
                        </div>
                        <div class="mt-3 text-center">
                             @if(auth()->user()->role === 'Admin')
                                <form method="POST" action="{{ route('billing.update-payment-status', $billing) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="payment_status" value="{{ $billing->payment_status === 'paid' ? 'unpaid' : 'paid' }}">
                                    <button type="submit" class="btn btn-{{ $billing->payment_status === 'paid' ? 'warning' : 'success' }} btn-sm">
                                        Mark as {{ $billing->payment_status === 'paid' ? 'Unpaid' : 'Paid' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="text-main mt-4"><i class="fas fa-tools me-2"></i>Parts & Materials Used</h6>
                @if($billing->serviceRequest->purchases->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($billing->serviceRequest->purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->item->item_name }}</td>
                                    <td>{{ $purchase->quantity }}</td>
                                    <td>₱{{ number_format($purchase->price_at_purchase, 2) }}</td>
                                    <td>₱{{ number_format($purchase->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-main">No parts were used for this service request.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header text-main">
                <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Customer & Service Info</h5>
            </div>
            <div class="card-body text-main">
                <p><strong>Customer:</strong> {{ $billing->serviceRequest->customer->user->full_name }}</p>
                <p><strong>Device:</strong> {{ $billing->serviceRequest->device_type }}</p>
                <p><strong>Problem:</strong> {{ $billing->serviceRequest->problem_description }}</p>
                <hr>
                <p><strong>Date Created:</strong> {{ $billing->created_at->format('M d, Y') }}</p>
                <p><strong>Last Updated:</strong> {{ $billing->updated_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection