@extends('layouts.app')

@section('title', 'Inventory - TeknoHub')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-boxes me-2"></i>Inventory Management</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('inventory.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Item
        </a>
    </div>
</div>

<div class="row">
    @forelse($items as $item)
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="card-title mb-0">{{ $item->item_name }}</h5>
                    <span class="badge bg-primary">{{ $item->category }}</span>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Price:</span>
                        <strong>${{ number_format($item->price, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Stock:</span>
                        <strong class="{{ $item->stock_quantity < 5 ? 'text-warning' : 'text-success' }}">
                            {{ $item->stock_quantity }}
                        </strong>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('inventory.edit', $item) }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <form action="{{ route('inventory.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this item?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <small class="text-muted">Last updated: {{ $item->updated_at->diffForHumans() }}</small>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No inventory items found</h5>
                <p class="text-muted">Start by adding your first inventory item.</p>
                <a href="{{ route('inventory.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-2"></i>Add First Item
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection@extends('layouts.app')

@section('title', 'Inventory - TeknoHub')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-boxes me-2"></i>Inventory Management</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('inventory.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Item
        </a>
    </div>
</div>

<div class="row">
    @forelse($items as $item)
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="card-title mb-0">{{ $item->item_name }}</h5>
                    <span class="badge bg-primary">{{ $item->category }}</span>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Price:</span>
                        <strong>${{ number_format($item->price, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Stock:</span>
                        <strong class="{{ $item->stock_quantity < 5 ? 'text-warning' : 'text-success' }}">
                            {{ $item->stock_quantity }}
                        </strong>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('inventory.edit', $item) }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <form action="{{ route('inventory.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this item?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <small class="text-muted">Last updated: {{ $item->updated_at->diffForHumans() }}</small>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No inventory items found</h5>
                <p class="text-muted">Start by adding your first inventory item.</p>
                <a href="{{ route('inventory.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-2"></i>Add First Item
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection