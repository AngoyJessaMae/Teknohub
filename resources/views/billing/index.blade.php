@extends('layouts.app')

@section('title', 'Billing - TeknoHub')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-file-invoice-dollar me-2"></i>Billing Management</h2>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group">
            <button class="btn btn-outline-primary" onclick="filterByStatus('all')">All</button>
            <button class="btn btn-outline-warning" onclick="filterByStatus('pending')">Pending</button>
            <button class="btn btn-outline-danger" onclick="filterByStatus('unpaid')">Unpaid</button>
            <button class="btn btn-outline-success" onclick="filterByStatus('paid')">Paid</button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped" id="billingTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service ID</th>
                        <th>Customer</th>
                        <th>Labor Fee</th>
                        <th>Parts Fee</th>
                        <th>Total Amount</th>
                        <th>Payment Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billings as $billing)
                    <tr data-status="{{ $billing->payment_status }}">
                        <td>{{ $billing->billing_id }}</td>
                        <td>{{ $billing->service_id }}</td>
                        <td>{{ $billing->serviceRequest->customer->user->full_name }}</td>
                        <td>${{ number_format($billing->labor_fee, 2) }}</td>
                        <td>${{ number_format($billing->parts_fee, 2) }}</td>
                        <td><strong>${{ number_format($billing->total_amount, 2) }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $billing->payment_status === 'paid' ? 'success' : ($billing->payment_status === 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($billing->payment_status) }}
                            </span>
                        </td>
                        <td>{{ $billing->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('billing.show', $billing) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->role === 'admin')
                                <form method="POST" action="{{ route('billing.update-payment-status', $billing) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="payment_status" value="{{ $billing->payment_status === 'paid' ? 'unpaid' : 'paid' }}">
                                    <button type="submit" class="btn btn-outline-{{ $billing->payment_status === 'paid' ? 'warning' : 'success' }} btn-sm">
                                        <i class="fas fa-{{ $billing->payment_status === 'paid' ? 'undo' : 'check' }}"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No billing records found</td>
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
                <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                <h3 class="card-title">${{ number_format($billings->where('payment_status', 'paid')->sum('total_amount'), 2) }}</h3>
                <p class="card-text text-muted">Total Paid</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                <h3 class="card-title">${{ number_format($billings->where('payment_status', 'pending')->sum('total_amount'), 2) }}</h3>
                <p class="card-text text-muted">Pending Payment</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                <h3 class="card-title">${{ number_format($billings->where('payment_status', 'unpaid')->sum('total_amount'), 2) }}</h3>
                <p class="card-text text-muted">Unpaid Amount</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                <h3 class="card-title">${{ number_format($billings->sum('total_amount'), 2) }}</h3>
                <p class="card-text text-muted">Total Revenue</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function filterByStatus(status) {
        const table = document.getElementById('billingTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let row of rows) {
            if (status === 'all' || row.getAttribute('data-status') === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }

        // Update button states
        const buttons = document.querySelectorAll('.btn-group button');
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
    }
</script>
@endpush