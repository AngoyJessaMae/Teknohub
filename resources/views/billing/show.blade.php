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
                            @if($billing->payment_mode)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Payment Method:
                                <span>{{ $billing->payment_mode }}</span>
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-main">Payment Status</h6>
                        <div class="d-grid">
                            <span class="badge fs-5 bg-{{ strtolower($billing->payment_status) === 'paid' ? 'success' : (strtolower($billing->payment_status) === 'pending' ? 'warning' : 'danger') }}">
                                <i class="fas {{ strtolower($billing->payment_status) === 'paid' ? 'fa-check-circle' : (strtolower($billing->payment_status) === 'pending' ? 'fa-clock' : 'fa-times-circle') }} me-1"></i>
                                {{ ucfirst($billing->payment_status) }}
                            </span>
                        </div>
                        <!-- DEBUG: User Role is [{{ auth()->user()->role ?? 'NOT LOGGED IN' }}] -->
                        <div class="mt-3 text-center">
                            @if(in_array(auth()->user()->role, ['admin', 'employee']))
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
                <p><strong>Billing Employee:</strong> {{ $billing->employee ? $billing->employee->employee_id . ' - ' . $billing->employee->user->full_name : 'N/A' }}</p>
                <p><strong>Date Billed:</strong> {{ $billing->date_billed ? \Carbon\Carbon::parse($billing->date_billed)->format('M d, Y') : 'N/A' }}</p>
                <p><strong>Date Created:</strong> {{ $billing->created_at->format('M d, Y') }}</p>
                <p><strong>Last Updated:</strong> {{ $billing->updated_at->format('M d, Y') }}</p>
                <p>DEBUG: Payment Status is [{{ $billing->payment_status }}]</p>
            </div>
        </div>

        @if(auth()->user()->role === 'customer' && strtolower($billing->payment_status) !== 'paid')
        <div class="card mt-4">
            <div class="card-header text-main">
                <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Submit Payment</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('billing.submit-payment', $billing) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="payment_mode" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_mode" name="payment_mode" required>
                            @php
                            $selectedPaymentMode = request()->query('payment_mode', 'Cash');
                            $paymentOptions = ['Cash', 'G-Cash', 'PayMaya', 'Bank Transfer', 'Credit Card', 'Debit Card'];
                            @endphp
                            @foreach($paymentOptions as $option)
                            <option value="{{ $option }}" {{ $selectedPaymentMode === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="receipt-upload-section">
                        <label for="receipt" class="form-label">Upload Receipt</label>
                        <input class="form-control" type="file" id="receipt" name="receipt">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Payment</button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentModeSelect = document.getElementById('payment_mode');
        const receiptUploadSection = document.getElementById('receipt-upload-section');
        const receiptInput = document.getElementById('receipt');

        function toggleReceiptSection() {
            if (paymentModeSelect.value === 'Cash') {
                receiptUploadSection.style.display = 'none';
                receiptInput.required = false;
            } else {
                receiptUploadahan.style.display = 'block';
                receiptInput.required = true;
            }
        }

        // Initial check
        toggleReceiptSection();

        // Listen for changes
        paymentModeSelect.addEventListener('change', toggleReceiptSection);
    });
</script>
@endpush