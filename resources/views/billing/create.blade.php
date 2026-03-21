@extends('layouts.app')

@section('title', 'Generate Billing - TeknoHub')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-main">
                <h5 class="mb-0">
                    <i class="fas fa-file-invoice me-2"></i>Generate Billing - Service Request #{{ $serviceRequest->service_id }}
                </h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('service-requests.billing.store', $serviceRequest) }}">
                    @csrf

                    <!-- SR Info -->
                    <div class="mb-4">
                        <h6 class="text-main">Service Request Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Customer:</strong> {{ $serviceRequest->customer->user->full_name }}</p>
                                <p class="mb-1"><strong>Device:</strong> {{ $serviceRequest->device_type }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Status:</strong> 
                                    <span class="badge bg-{{ $serviceRequest->status === 'completed' ? 'success' : ($serviceRequest->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($serviceRequest->status) }}
                                    </span>
                                </p>
                                <p class="mb-0"><strong>Employee:</strong> {{ $serviceRequest->employee->user->full_name ?? 'Not assigned' }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- employee input billing dets -->
                    <div class="mb-4">
                        <h6 class="text-main">Billing Summary</h6>
                        <div class="bg-light p-3 rounded">
                            <div class="mb-3">
                                <label for="labor_fee" class="form-label text-main">Labor Fee <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control @error('labor_fee') is-invalid @enderror" 
                                           id="labor_fee" name="labor_fee" 
                                           value="{{ old('labor_fee', $laborFee) }}" 
                                           step="0.01" min="0" required
                                           oninput="calculateTotal()">
                                </div>
                                @error('labor_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="parts_fee" class="form-label text-main">Parts Fee <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control @error('parts_fee') is-invalid @enderror" 
                                           id="parts_fee" name="parts_fee" 
                                           value="{{ old('parts_fee', $partsTotal) }}" 
                                           step="0.01" min="0" required
                                           oninput="calculateTotal()">
                                </div>
                                @error('parts_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            @if($serviceRequest->purchases->count() > 0)
                            <div class="mb-3">
                                <small class="text-muted">Parts used:</small>
                                <ul class="mb-0 small text-muted">
                                    @foreach($serviceRequest->purchases as $purchase)
                                    <li>{{ $purchase->item->item_name }} (x{{ $purchase->quantity }}) - ₱{{ number_format($purchase->total_price, 2) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="h5 mb-0">Total Amount:</span>
                                <strong class="h5 mb-0 text-primary" id="total_amount_display">₱{{ number_format($totalAmount, 2) }}</strong>
                            </div>
                            <input type="hidden" id="total_amount" name="total_amount" value="{{ $totalAmount }}">
                        </div>
                    </div>

                    <hr>

                    <!-- warranty -->
                    <div class="mb-4">
                        <h6 class="text-main">Additional Information</h6>
                        
                        <div class="mb-3">
                            <label for="warranty" class="form-label text-main">Warranty (Optional)</label>
                            <input type="text" class="form-control @error('warranty') is-invalid @enderror" 
                                   id="warranty" name="warranty" 
                                   placeholder="e.g., 1 year parts warranty"
                                   value="{{ old('warranty') }}">
                            @error('warranty')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr>

                    <!-- payment dets -->
                    <div class="mb-4">
                        <h6 class="text-main">Payment Details</h6>
                        
                        <div class="mb-3">
                            <label for="payment_mode" class="form-label text-main">Mode of Payment <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_mode') is-invalid @enderror" id="payment_mode" name="payment_mode" required>
                                <option value="">Select Payment Mode</option>
                                <option value="Cash" {{ old('payment_mode') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Credit Card" {{ old('payment_mode') === 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="Debit Card" {{ old('payment_mode') === 'Debit Card' ? 'selected' : '' }}>Debit Card</option>
                                <option value="G-Cash" {{ old('payment_mode') === 'G-Cash' ? 'selected' : '' }}>G-Cash</option>
                                <option value="PayMaya" {{ old('payment_mode') === 'PayMaya' ? 'selected' : '' }}>PayMaya</option>
                                <option value="Bank Transfer" {{ old('payment_mode') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            </select>
                            @error('payment_mode')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="payment_status" class="form-label text-main">Payment Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status" required onchange="togglePaymentDate()">
                                <option value="Pending" {{ old('payment_status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Paid" {{ old('payment_status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Unpaid" {{ old('payment_status') === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                            </select>
                            @error('payment_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="payment_date_section" style="display: none;">
                            <label for="payment_date" class="form-label text-main">Payment Date</label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}">
                            @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('service-requests.show', $serviceRequest) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Bill
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header text-main">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Billing Guidelines
                </h5>
            </div>
            <div class="card-body text-main">
                <h6>Employee Input Required</h6>
                <p class="text-muted small">Enter the labor fee and parts fee to calculate the total bill.</p>

                <h6>Payment Modes</h6>
                <ul class="text-muted small">
                    <li><strong>Cash</strong> - Pay at the store</li>
                    <li><strong>Credit/Debit Card</strong> - Visa, Mastercard</li>
                    <li><strong>G-Cash</strong> - GCash mobile wallet</li>
                    <li><strong>PayMaya</strong> - Maya mobile wallet</li>
                    <li><strong>Bank Transfer</strong> - Direct bank transfer</li>
                </ul>

                <h6>Payment Status</h6>
                <ul class="text-muted small">
                    <li><strong>Pending</strong> - Awaiting payment</li>
                    <li><strong>Paid</strong> - Payment received</li>
                    <li><strong>Unpaid</strong> - Payment overdue</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePaymentDate() {
        var statusSelect = document.getElementById('payment_status');
        var dateSection = document.getElementById('payment_date_section');
        
        if (statusSelect.value === 'Paid') {
            dateSection.style.display = 'block';
        } else {
            dateSection.style.display = 'none';
        }
    }

    function calculateTotal() {
        var laborFee = parseFloat(document.getElementById('labor_fee').value) || 0;
        var partsFee = parseFloat(document.getElementById('parts_fee').value) || 0;
        var totalAmount = laborFee + partsFee;
        
        document.getElementById('total_amount_display').textContent = '₱' + totalAmount.toFixed(2);
        document.getElementById('total_amount').value = totalAmount.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function() {
        togglePaymentDate();
    });
</script>
@endsection
