@extends('layouts.app')

@section('title', 'Edit Inventory Item - TeknoHub')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center text-main">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Inventory Item
                </h5>
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('inventory.update', $item->item_id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label text-main">Item Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" value="{{ old('name', $item->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label text-main">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description" name="description">{{ old('description', $item->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label text-main">Price ($)</label>
                            <input type="number" step="0.01" min="0"
                                class="form-control @error('price') is-invalid @enderror"
                                id="price" name="price" value="{{ old('price', $item->price) }}" required>
                            @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label text-main">Stock Quantity</label>
                            <input type="number" min="0"
                                class="form-control @error('quantity') is-invalid @enderror"
                                id="quantity" name="quantity" value="{{ old('quantity', $item->quantity) }}" required>
                            @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Item
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
                    <i class="fas fa-chart-bar me-2"></i>Item Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-main">Total Used:</span>
                        <strong class="text-main">{{ $item->purchases->sum('quantity') }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-main">Total Revenue:</span>
                        <strong class="text-main">${{ number_format($item->purchases->sum('total_price'), 2) }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-main">Stock Status:</span>
                        <strong class="{{ $item->quantity < 5 ? 'text-danger' : 'text-success' }}">
                            {{ $item->quantity < 5 ? 'Low Stock' : 'In Stock' }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header text-main">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Recent Activity
                </h5>
            </div>
            <div class="card-body">
                @if($item->purchases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Qty</th>
                                <th>Service</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->purchases->take(5) as $purchase)
                            <tr>
                                <td>{{ $purchase->created_at->format('M d') }}</td>
                                <td>{{ $purchase->quantity }}</td>
                                <td>#{{ $purchase->service_id }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-main small">No recent activity for this item.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection