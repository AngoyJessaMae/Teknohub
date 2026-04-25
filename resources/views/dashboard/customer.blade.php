@extends('layouts.app')

@section('title', 'Customer Dashboard - TeknoHub')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-wrench fa-2x text-primary mb-2"></i>
                <h3 class="card-title text-main">{{ $totalRequests }}</h3>
                <p class="card-text text-muted">Total Requests</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                <h3 class="card-title text-main">{{ $pendingRepairs }}</h3>
                <p class="card-text text-muted">Pending Repairs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h3 class="card-title text-main">{{ $completedRepairs }}</h3>
                <p class="card-text text-muted">Completed Repairs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-plus fa-2x text-info mb-2"></i>
                <h3 class="card-title text-main">New</h3>
                <p class="card-text text-muted">Service Request</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header text-main d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>My Service Requests
                </h5>
                <a href="{{ route('service-requests.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Request
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Device</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Total Cost</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customerRequests as $request)
                            <tr>
                                <td>{{ $request->service_id }}</td>
                                <td>{{ $request->device_type }}</td>
                                <td>{{ Str::limit($request->device_description, 50) }}</td>
                                <td>
                                    <span class="badge bg-{{ $request->status === 'completed' ? 'success' : ($request->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td>{{ $request->employee ? $request->employee->user->full_name : 'Unassigned' }}</td>
                                <td>₱{{ number_format($request->billing->total_amount ?? 0, 2) }}</td>
                                <td>
                                    <a href="{{ route('service-requests.show', $request) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($request->billing && $request->billing->exists && strtolower($request->billing->payment_status) !== 'paid')
                                    <div class="dropdown d-inline" data-billing-id="{{ $request->billing->billing_id }}">
                                        <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-money-bill-wave"></i> Pay
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item pay-option" href="#" data-billing-id="{{ $request->billing->billing_id }}" data-payment-mode="Cash">Cash</a></li>
                                            <li><a class="dropdown-item pay-option" href="#" data-billing-id="{{ $request->billing->billing_id }}" data-payment-mode="G-Cash">G-Cash</a></li>
                                            <li><a class="dropdown-item pay-option" href="#" data-billing-id="{{ $request->billing->billing_id }}" data-payment-mode="PayMaya">PayMaya</a></li>
                                            <li><a class="dropdown-item pay-option" href="#" data-billing-id="{{ $request->billing->billing_id }}" data-payment-mode="Bank Transfer">Bank Transfer</a></li>
                                            <li><a class="dropdown-item pay-option" href="#" data-billing-id="{{ $request->billing->billing_id }}" data-payment-mode="Credit Card">Credit Card</a></li>
                                            <li><a class="dropdown-item pay-option" href="#" data-billing-id="{{ $request->billing->billing_id }}" data-payment-mode="Debit Card">Debit Card</a></li>
                                        </ul>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No service requests found. Create your first request!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="payment-form-container">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Submit Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @php
                $paymentChannels = config('payment.channels');
                @endphp
                <form id="paymentForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div id="payment-error" class="alert alert-danger" style="display: none;"></div>
                        <input type="hidden" name="billing_id" id="modal_billing_id">
                        <div class="mb-3">
                            <label for="modal_payment_mode" class="form-label">Payment Method</label>
                            <input type="text" class="form-control" id="modal_payment_mode" name="payment_mode" readonly>
                        </div>
                        <div class="mb-3" id="modal-payment-instructions">
                            <div class="alert alert-info mb-0" role="alert">
                                <strong id="modal-payment-instructions-title">Payment Instructions</strong>
                                <div id="modal-payment-instructions-text" class="small"></div>
                            </div>
                        </div>
                        <div class="mb-3" id="modal-receipt-section">
                            <label for="modal_receipt" class="form-label">Upload Proof of Payment</label>
                            <input class="form-control" type="file" id="modal_receipt" name="receipt">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
            <div id="success-message-container" style="display: none;">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Submitted</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.reload()"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <p id="success-text" class="lead"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="window.location.reload()">Done</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentModalEl = document.getElementById('paymentModal');
        const paymentModal = new bootstrap.Modal(paymentModalEl);
        const paymentForm = document.getElementById('paymentForm');
        const modalBillingIdInput = document.getElementById('modal_billing_id');
        const modalPaymentMode = document.getElementById('modal_payment_mode');
        const modalReceiptSection = document.getElementById('modal-receipt-section');
        const modalReceiptInput = document.getElementById('modal_receipt');
        const paymentError = document.getElementById('payment-error');
        const instructionsTitle = document.getElementById('modal-payment-instructions-title');
        const instructionsText = document.getElementById('modal-payment-instructions-text');
        const paymentChannels = @json($paymentChannels ?? []);

        document.querySelectorAll('.pay-option').forEach(item => {
            item.addEventListener('click', function(event) {
                event.preventDefault();

                // Reset error message on open
                paymentError.style.display = 'none';

                const dropdownDiv = this.closest('.dropdown');
                const billingId = this.getAttribute('data-billing-id') || (dropdownDiv ? dropdownDiv.getAttribute('data-billing-id') : null);
                const paymentMode = this.getAttribute('data-payment-mode');

                if (!billingId) {
                    // This error should no longer appear with the new structure
                    console.error('Could not process payment. Billing ID is missing.');
                    return;
                }

                let actionUrl = "{{ route('billing.submit-payment', ['billing' => ':billing_id']) }}";
                actionUrl = actionUrl.replace(':billing_id', billingId);
                paymentForm.action = actionUrl;

                modalBillingIdInput.value = billingId;
                modalPaymentMode.value = paymentMode;

                if (paymentMode === 'Cash') {
                    modalReceiptSection.style.display = 'none';
                    modalReceiptInput.required = false;
                } else {
                    modalReceiptSection.style.display = 'block';
                    modalReceiptInput.required = true;
                }

                const channel = paymentChannels[paymentMode];
                if (channel) {
                    instructionsTitle.textContent = channel.label || 'Payment Instructions';
                    instructionsText.textContent = channel.details || '';
                }

                paymentModal.show();
            });
        });

        paymentModalEl.addEventListener('hidden.bs.modal', function() {
            // Reset the modal to its initial state when closed
            document.getElementById('payment-form-container').style.display = 'block';
            document.getElementById('success-message-container').style.display = 'none';
            paymentForm.reset();
            paymentError.style.display = 'none';
        });
    });
</script>
@endpush