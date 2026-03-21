@extends('layouts.app')

@section('title', 'Add Inventory Item - TeknoHub')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 text-main">
                    <i class="fas fa-plus me-2"></i>Add New Inventory Item
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('inventory.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="item_name" class="form-label text-main">Item Name</label>
                        <input type="text" class="form-control @error('item_name') is-invalid @enderror"
                            id="item_name" name="item_name" value="{{ old('item_name') }}" required>
                        @error('item_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label text-main">Category</label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Hardware" {{ old('category') === 'Hardware' ? 'selected' : '' }}>Hardware</option>
                            <option value="Software" {{ old('category') === 'Software' ? 'selected' : '' }}>Software</option>
                            <option value="Accessories" {{ old('category') === 'Accessories' ? 'selected' : '' }}>Accessories</option>
                            <option value="Components" {{ old('category') === 'Components' ? 'selected' : '' }}>Components</option>
                            <option value="Tools" {{ old('category') === 'Tools' ? 'selected' : '' }}>Tools</option>
                            <option value="Other" {{ old('category') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label text-main">Price ($)</label>
                            <input type="number" step="0.01" min="0"
                                class="form-control @error('price') is-invalid @enderror"
                                id="price" name="price" value="{{ old('price') }}" required>
                            @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stock_quantity" class="form-label text-main">Stock Quantity</label>
                            <input type="number" min="0"
                                class="form-control @error('stock_quantity') is-invalid @enderror"
                                id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity') }}" required>
                            @error('stock_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Add Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 text-main">
                    <i class="fas fa-info-circle me-2"></i>Inventory Guidelines
                </h5>
            </div>
            <div class="card-body text-main">
                <h6>Item Categories</h6>
                <ul class="text-muted small">
                    <li><strong>Hardware:</strong> Physical computer parts</li>
                    <li><strong>Software:</strong> Digital licenses and tools</li>
                    <li><strong>Accessories:</strong> Cables, adapters, etc.</li>
                    <li><strong>Components:</strong> Internal parts and chips</li>
                    <li><strong>Tools:</strong> Repair and diagnostic tools</li>
                </ul>

                <h6>Pricing Guidelines</h6>
                <p class="text-muted small">Set competitive prices that cover your costs and provide reasonable profit margins.</p>

                <h6>Stock Management</h6>
                <p class="text-muted small">Keep track of inventory levels and reorder when stock runs low to avoid service delays.</p>
            </div>
        </div>
    </div>
</div>
@endsection