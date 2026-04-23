@extends('layouts.app')

@section('title', 'Billing - TeknoHub')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-file-invoice-dollar me-2 text-main"></i>Billing Management</h2>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group">
            <button class="btn btn-outline-primary active" onclick="filterByStatus('all', event)">All</button>
            <button class="btn btn-outline-warning" onclick="filterByStatus('pending', event)">Pending</button>
            <button class="btn btn-outline-danger" onclick="filterByStatus('unpaid', event)">Unpaid</button>
            <button class="btn btn-outline-success" onclick="filterByStatus('paid', event)">Paid</button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body text-main">
        <div class="table-responsive">
            <table class="table table-striped" id="billingTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service ID</th>
                        <th>Customer</th>
                        <th>Labor Fee</th>
                        <th>Parts Fee</th>
                        <th>Total Amount</th>
                        <th>Payment Status</th>
                        <th>Billing Employee</th>
                        <th>Date Billed</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billings as $billing)
                    <tr data-status="{{ strtolower($billing->payment_status) }}">
                        <td>{{ $billing->billing_id }}</td>
                        <td>{{ $billing->service_id }}</td>
                        <td>{{ $billing->serviceRequest->customer->user->full_name }}</td>
                        <td>₱{{ number_format($billing->labor_fee, 2) }}</td>
                        <td>₱{{ number_format($billing->parts_fee, 2) }}</td>
                        <td><strong>₱{{ number_format($billing->total_amount, 2) }}</strong></td>
                        <td>
                            <span class="badge bg-{{ strtolower($billing->payment_status) === 'paid' ? 'success' : (strtolower($billing->payment_status) === 'pending' ? 'warning' : 'danger') }}">
                                <i class="fas {{ strtolower($billing->payment_status) === 'paid' ? 'fa-check-circle' : (strtolower($billing->payment_status) === 'pending' ? 'fa-clock' : 'fa-times-circle') }} me-1"></i>
                                {{ ucfirst($billing->payment_status) }}
                            </span>
                        </td>
                        <td>
                            {{ $billing->employee ? $billing->employee->employee_id . ' - ' . $billing->employee->user->full_name : 'N/A' }}
                        </td>
                        <td>{{ $billing->date_billed ? \Carbon\Carbon::parse($billing->date_billed)->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ $billing->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('billing.show', $billing) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                {{-- Simplified for debugging --}}
                                @if(auth()->user()->role === 'customer')
                                <a href="{{ route('billing.show', $billing) }}" class="btn btn-outline-success">
                                    <i class="fas fa-money-bill-wave"></i> Pay
                                </a>
                                @endif
                                @if(in_array(auth()->user()->role, ['admin', 'employee']))
                                <form method="POST" action="{{ route('billing.update-payment-status', $billing) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="payment_status" value="{{ $billing->payment_status === 'paid' ? 'unpaid' : 'paid' }}">
                                    <button type="submit" class="btn btn-outline-{{ $billing->payment_status === 'paid' ? 'warning' : 'success' }} btn-sm" title="Mark as {{ $billing->payment_status === 'paid' ? 'Unpaid' : 'Paid' }}">
                                        <i class="fas fa-{{ $billing->payment_status === 'paid' ? 'undo' : 'check' }}"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted">No billing records found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-peso-sign fa-2x text-success mb-2" data-status-icon="paid"></i>
                <h3 class="card-title text-main" data-status-total="paid">₱{{ number_format($billings->where('payment_status', 'Paid')->sum('total_amount'), 2) }}</h3>
                <p class="card-text text-main" data-status-label="paid">Total Paid</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-clock fa-2x text-warning mb-2" data-status-icon="pending"></i>
                <h3 class="card-title text-main" data-status-total="pending">₱{{ number_format($billings->where('payment_status', 'Pending')->sum('total_amount'), 2) }}</h3>
                <p class="card-text text-main" data-status-label="pending">Pending Payment</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-times-circle fa-2x text-danger mb-2" data-status-icon="unpaid"></i>
                <h3 class="card-title text-main" data-status-total="unpaid">₱{{ number_format($billings->where('payment_status', 'Unpaid')->sum('total_amount'), 2) }}</h3>
                <p class="card-text text-main" data-status-label="unpaid">Unpaid Amount</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-chart-line fa-2x text-info mb-2" data-status-icon="all"></i>
                <h3 class="card-title text-main" data-status-total="all">₱{{ number_format($billings->sum('total_amount'), 2) }}</h3>
                <p class="card-text text-main" data-status-label="all">Total Revenue</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function filterByStatus(status, event) {
        const table = document.getElementById('billingTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let row of rows) {
            const show = status === 'all' || row.getAttribute('data-status') === status;
            row.style.display = show ? '' : 'none';
        }

        const buttons = document.querySelectorAll('[onclick^="filterByStatus"]');
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        filterByStatus('all', {
            target: document.querySelector('.btn.active')
        });
    });
</script>
@endpush