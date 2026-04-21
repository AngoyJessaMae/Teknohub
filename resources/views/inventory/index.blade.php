@extends('layouts.app')

@section('title', 'Inventory - TeknoHub')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2 class="text-main"><i class="fas fa-boxes me-2"></i>Inventory Management</h2>
    </div>
    <div class="col-md-6 text-end">
        @if(auth()->user()->role !== 'Customer')
        <a href="{{ route('inventory.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Item
        </a>
        @endif
    </div>
</div>

<div class="row">
    @forelse($items as $item)
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="card-title mb-0 text-main">{{ $item->item_name }}</h5>
                    <span class="badge bg-primary">{{ $item->category }}</span>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-main">Price:</span>
                        <strong class="text-main">₱{{ number_format($item->price, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-main">Stock:</span>
                        <strong class="{{ $item->stock_quantity < 5 ? 'text-warning' : 'text-success' }}">
                            {{ $item->stock_quantity }}
                        </strong>
                    </div>
                </div>

                @if(auth()->user()->role !== 'Customer')
                <div class="d-flex justify-content-between">
                    <a href="{{ route('inventory.edit', $item) }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal{{ $item->item_id }}">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>

                </div>

                <!-- delete Modal -->
                <div class="modal fade" id="deleteConfirmModal{{ $item->item_id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-danger text-white">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <h5 class="modal-title mb-0">Confirm Delete</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center py-4">
                                <i class="fas fa-trash-alt fa-3x text-danger mb-3 opacity-75"></i>
                                <h6 class="text-main mb-1">Are you sure?</h6>
                                <p class="text-muted mb-0">This action cannot be undone.</p>
                                <p class="fw-bold text-main mt-2">{{ $item->item_name }}</p>
                            </div>
                            <div class="modal-footer border-0 justify-content-center">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </button>
                                <form action="{{ route('inventory.destroy', $item) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-1"></i>Yes, Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="card-footer bg-transparent">
                <small class="text-main">Last updated: {{ $item->updated_at->diffForHumans() }}</small>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5 class="text-main">No inventory items found</h5>
                <p class="text-main">Start by adding your first inventory item.</p>
                @if(auth()->user()->role !== 'Customer')
                <a href="{{ route('inventory.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-2"></i>Add First Item
                </a>
                @endif
            </div>
        </div>
    </div>
    @endforelse
</div>
@section('scripts')
<script>
let currentDeleteItemId = null;
let currentDeleteItemName = null;

function openDeleteModal(itemId, itemName) {
    currentDeleteItemId = itemId;
    currentDeleteItemName = itemName;
    document.getElementById('deleteItemName').textContent = itemName;
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    modal.show();
}

function confirmDelete() {
    const form = document.getElementById('deleteForm');
    form.action = form.action.replace(':ITEM_ID_PLACEHOLDER', currentDeleteItemId);
    form.submit();
}
</script>
@endsection

<!-- Delete confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <h5 class="modal-title mb-0">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt fa-3x text-danger mb-3 opacity-75"></i>
                <h6 class="text-main mb-1">Are you sure?</h6>
                <p class="text-muted mb-0">This action cannot be undone.</p>
                <p class="fw-bold text-main mt-2" id="deleteItemName"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash me-1"></i>Yes, Delete Item
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
