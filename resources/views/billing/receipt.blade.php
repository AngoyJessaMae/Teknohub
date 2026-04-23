@extends('layouts.app')

@section('title', 'Official Receipt - TeknoHub')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-receipt me-2"></i>Official Receipt</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-main">TeknoHub</h5>
                            <p class="mb-0">123 Tech Street, Innovation City</p>
                            <p>contact@teknohub.com</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0"><strong>Receipt #:</strong> {{ str_pad($billing->billing_id, 8, '0', STR_PAD_LEFT) }}</p>
                            <p><strong>Date Paid:</strong> {{ \Carbon\Carbon::parse($billing->payment_date)->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-main">Bill To:</h6>
                            <p class="mb-0">{{ $billing->serviceRequest->customer->user->full_name }}</p>
                            <p>{{ $billing->serviceRequest->customer->user->email }}</p>
                        </div>
                    </div>

                    <h6 class="text-main">Billing Summary</h6>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Labor Fee</td>
                                <td class="text-end">₱{{ number_format($billing->labor_fee, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Parts Fee</td>
                                <td class="text-end">₱{{ number_format($billing->parts_fee, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-end">Total Amount:</th>
                                <th class="text-end">₱{{ number_format($billing->total_amount, 2) }}</th>
                            </tr>
                            <tr>
                                <th class="text-end">Payment Method:</th>
                                <th class="text-end">{{ $billing->payment_mode }}</th>
                            </tr>
                            <tr class="bg-light">
                                <th class="text-end text-success">Amount Paid:</th>
                                <th class="text-end text-success">₱{{ number_format($billing->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="text-center mt-4">
                        <p class="text-success"><i class="fas fa-check-circle"></i> Payment Confirmed. Thank you for your business!</p>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('billing.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Billing
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="fas fa-print me-1"></i>Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Official Receipt - TeknoHub')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-receipt me-2"></i>Official Receipt</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-main">TeknoHub</h5>
                            <p class="mb-0">123 Tech Street, Innovation City</p>
                            <p>contact@teknohub.com</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0"><strong>Receipt #:</strong> {{ str_pad($billing->billing_id, 8, '0', STR_PAD_LEFT) }}</p>
                            <p><strong>Date Paid:</strong> {{ \Carbon\Carbon::parse($billing->payment_date)->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-main">Bill To:</h6>
                            <p class="mb-0">{{ $billing->serviceRequest->customer->user->full_name }}</p>
                            <p>{{ $billing->serviceRequest->customer->user->email }}</p>
                        </div>
                    </div>

                    <h6 class="text-main">Billing Summary</h6>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Labor Fee</td>
                                <td class="text-end">₱{{ number_format($billing->labor_fee, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Parts Fee</td>
                                <td class="text-end">₱{{ number_format($billing->parts_fee, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-end">Total Amount:</th>
                                <th class="text-end">₱{{ number_format($billing->total_amount, 2) }}</th>
                            </tr>
                            <tr>
                                <th class="text-end">Payment Method:</th>
                                <th class="text-end">{{ $billing->payment_mode }}</th>
                            </tr>
                            <tr class="bg-light">
                                <th class="text-end text-success">Amount Paid:</th>
                                <th class="text-end text-success">₱{{ number_format($billing->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="text-center mt-4">
                        <p class="text-success"><i class="fas fa-check-circle"></i> Payment Confirmed. Thank you for your business!</p>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('billing.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Billing
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="fas fa-print me-1"></i>Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Official Receipt - TeknoHub')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-receipt me-2"></i>Official Receipt</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-main">TeknoHub</h5>
                            <p class="mb-0">123 Tech Street, Innovation City</p>
                            <p>contact@teknohub.com</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0"><strong>Receipt #:</strong> {{ str_pad($billing->billing_id, 8, '0', STR_PAD_LEFT) }}</p>
                            <p><strong>Date Paid:</strong> {{ \Carbon\Carbon::parse($billing->payment_date)->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-main">Bill To:</h6>
                            <p class="mb-0">{{ $billing->serviceRequest->customer->user->full_name }}</p>
                            <p>{{ $billing->serviceRequest->customer->user->email }}</p>
                        </div>
                    </div>

                    <h6 class="text-main">Billing Summary</h6>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Labor Fee</td>
                                <td class="text-end">₱{{ number_format($billing->labor_fee, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Parts Fee</td>
                                <td class="text-end">₱{{ number_format($billing->parts_fee, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-end">Total Amount:</th>
                                <th class="text-end">₱{{ number_format($billing->total_amount, 2) }}</th>
                            </tr>
                            <tr>
                                <th class="text-end">Payment Method:</th>
                                <th class="text-end">{{ $billing->payment_mode }}</th>
                            </tr>
                            <tr class="bg-light">
                                <th class="text-end text-success">Amount Paid:</th>
                                <th class="text-end text-success">₱{{ number_format($billing->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="text-center mt-4">
                        <p class="text-success"><i class="fas fa-check-circle"></i> Payment Confirmed. Thank you for your business!</p>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('billing.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Billing
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="fas fa-print me-1"></i>Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection