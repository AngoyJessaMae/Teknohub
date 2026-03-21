@extends('layouts.app')

@section('title', 'Create Service Request - TeknoHub')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-main">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Create New Service Request
                </h5>
            </div>
            <div class="card-body">
                <!-- validation errors -->
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('service-requests.store') }}">
                    @csrf

                    @auth
                        @if(Auth::user()->role === 'Employee')
                        <!-- select customer type -->
                        <div class="mb-4">
                            <label class="form-label text-main">Customer Type</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input customer-type" type="radio" name="customer_type" id="customer_existing" value="existing" {{ old('customer_type', 'existing') === 'existing' ? 'checked' : '' }}>
                                    <label class="form-check-label text-main" for="customer_existing">
                                        Existing Customer
                                    </label>
                                </div>
                                <div class="form-check text-main">
                                    <input class="form-check-input customer-type" type="radio" name="customer_type" id="customer_new" value="new" {{ old('customer_type') === 'new' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="customer_new">
                                        New Customer
                                    </label>
                                </div>
                            </div>
                            @error('customer_type')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- if customer exists -->
                        <div id="existing_customer_section" class="mb-3">
                            <label for="customer_id" class="form-label text-main">Select Customer</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                                <option value="">Select a Customer</option>
                                @forelse($customers as $customer)
                                    <option value="{{ $customer->customer_id }}" {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                                        {{ $customer->user->full_name ?? 'Unknown' }} ({{ $customer->user->email ?? 'No email' }})
                                    </option>
                                @empty
                                    <option value="">No customers found</option>
                                @endforelse
                            </select>
                            @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- for new customer -->
                        <div id="new_customer_section" class="mb-3 p-3 border rounded bg-light" style="display: none;">
                            <h6 class="mb-3 text-main">New Customer Information</h6>
                            <div class="mb-3">
                                <label for="new_customer_name" class="form-label text-main">Full Name</label>
                                <input type="text" class="form-control @error('new_customer_name') is-invalid @enderror" id="new_customer_name" name="new_customer_name" value="{{ old('new_customer_name') }}">
                                @error('new_customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="new_customer_email" class="form-label text-main">Email Address</label>
                                <input type="email" class="form-control @error('new_customer_email') is-invalid @enderror" id="new_customer_email" name="new_customer_email" value="{{ old('new_customer_email') }}">
                                @error('new_customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="new_customer_contact" class="form-label text-main">Contact Number</label>
                                <input type="text" class="form-control @error('new_customer_contact') is-invalid @enderror" id="new_customer_contact" name="new_customer_contact" value="{{ old('new_customer_contact') }}">
                                @error('new_customer_contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- assign staff to service -->
                        <div class="mb-3">
                            <label for="staff_id" class="form-label text-main">Assign to Staff</label>
                            <select class="form-select" id="staff_id" name="staff_id">
                                <option value="">Select Staff (Optional - defaults to you)</option>
                                @forelse($staff as $employee)
                                    <option value="{{ $employee->employee_id }}" {{ old('staff_id') == $employee->employee_id ? 'selected' : '' }}>
                                        {{ $employee->user->full_name ?? 'Unknown' }} - {{ $employee->job_title ?? 'Staff' }}
                                    </option>
                                @empty
                                    <option value="">No staff available</option>
                                @endforelse
                            </select>
                            <small class="text-muted">Leave blank to assign to yourself</small>
                        </div>
                        @endif
                    @endauth

<div class="mb-3">
                        <label for="service_type" class="form-label text-main">Service Type</label>
                        <select class="form-select @error('service_type') is-invalid @enderror" id="service_type" name="service_type" required>
                            <option value="">Select Service Type</option>
                            <option value="diagnostic" {{ old('service_type') === 'diagnostic' ? 'selected' : '' }}>Diagnostic</option>
                            <option value="hardware_repair" {{ old('service_type') === 'hardware_repair' ? 'selected' : '' }}>Hardware Repair</option>
                            <option value="software_install" {{ old('service_type') === 'software_install' ? 'selected' : '' }}>Software Installation</option>
                            <option value="cleaning" {{ old('service_type') === 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                            <option value="upgrade" {{ old('service_type') === 'upgrade' ? 'selected' : '' }}>Upgrade</option>
                            <option value="data_recovery" {{ old('service_type') === 'data_recovery' ? 'selected' : '' }}>Data Recovery</option>
                        </select>
                        @error('service_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="device_type" class="form-label text-main">Device Type</label>
                        <select class="form-select @error('device_type') is-invalid @enderror" id="device_type" name="device_type" required>
                            <option value="">Select Device Type</option>
                            <option value="Laptop" {{ old('device_type') === 'Laptop' ? 'selected' : '' }}>Laptop</option>
                            <option value="Desktop" {{ old('device_type') === 'Desktop' ? 'selected' : '' }}>Desktop</option>
                            <option value="Smartphone" {{ old('device_type') === 'Smartphone' ? 'selected' : '' }}>Smartphone</option>
                            <option value="Tablet" {{ old('device_type') === 'Tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="Printer" {{ old('device_type') === 'Printer' ? 'selected' : '' }}>Printer</option>
                            <option value="Monitor" {{ old('device_type') === 'Monitor' ? 'selected' : '' }}>Monitor</option>
                            <option value="Other" {{ old('device_type') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('device_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="device_description" class="form-label text-main">Device Description</label>
                        <textarea class="form-control @error('device_description') is-invalid @enderror"
                            id="device_description" name="device_description" rows="6"
                            placeholder="Describe your device and the issue in detail..." required>{{ old('device_description') }}</textarea>
                        @error('device_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="appointment_request" class="form-label text-main">Requested Appointment Date (Optional)</label>
                        <input type="datetime-local" class="form-control @error('appointment_request') is-invalid @enderror"
                            id="appointment_request" name="appointment_request"
                            value="{{ old('appointment_request') }}">
                        @error('appointment_request')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-main">Add Parts from Inventory (Optional)</label>
                        <select class="form-select" id="item_select" onchange="addPart()">
                            <option value="">No parts needed</option>
                            @foreach($items as $item)
                            <option value="{{ $item->item_id }}" data-price="{{ $item->price }}" data-name="{{ $item->item_name }}">
                                {{ $item->item_name }} (₱{{ number_format($item->price, 2) }} x{{ $item->stock_quantity }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="parts_list" class="mb-3" style="display: none;">
                        <h6>Selected Parts:</h6>
                        <ul id="parts_items" class="list-group"></ul>
                        <input type="hidden" id="parts_json" name="parts" value="">
                    </div>

                    <div class="mb-3">
                        <label for="priority_level" class="form-label text-main">Priority Level (Optional)</label>
                        <select class="form-select @error('priority_level') is-invalid @enderror" id="priority_level" name="priority_level">
                            <option value="Normal" {{ old('priority_level', 'Normal') === 'Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="High" {{ old('priority_level') === 'High' ? 'selected' : '' }}>High</option>
                            <option value="Urgent" {{ old('priority_level') === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="Low" {{ old('priority_level') === 'Low' ? 'selected' : '' }}>Low</option>
                        </select>
                        @error('priority_level')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Submit Request
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
                    <i class="fas fa-info-circle me-2"></i>Request Guidelines
                </h5>
            </div>
            <div class="card-body text-main">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Priority Level Guidelines</h6>
                <ul class="text-main small">
                    <li><strong>Normal:</strong> Standard service requests that are handled in the order they are received.</li>
                    <li><strong>High:</strong> The issue significantly impacts your ability to work, but you have a temporary workaround.</li>
                    <li><strong>Urgent:</strong> The issue completely prevents you from working and requires immediate attention.</li>
                    <li><strong>Low:</strong> The issue is minor and does not have a significant impact on your workflow.</li>
                </ul>
                <hr>

                <h6>Device Information</h6>
                <p class="text-muted small">Please provide as much detail as possible about your device and the issues you're experiencing.</p>

                <h6>Common Issues</h6>
                <ul class="text-muted small">
                    <li>Hardware malfunctions</li>
                    <li>Software problems</li>
                    <li>Performance issues</li>
                    <li>Physical damage</li>
                    <li>Connectivity problems</li>
                </ul>

                <h6>What to Include</h6>
                <ul class="text-muted small">
                    <li>Device model and specifications</li>
                    <li>When the problem started</li>
                    <li>Error messages (if any)</li>
                    <li>Steps to reproduce the issue</li>
                </ul>
            </div>
        </div>
    </div>
</div>


<script>
    function addPart() {
        const select = document.getElementById('item_select');
        const list = document.getElementById('parts_items');
        const partsList = document.getElementById('parts_list');
        
        if (select.value) {
            partsList.style.display = 'block';
            const option = select.options[select.selectedIndex];
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `
                <span>${option.dataset.name}</span>
                <div class="d-flex align-items-center gap-2">
                    <input type="number" class="form-control form-control-sm w-auto" min="1" value="1" onchange="updatePartTotal(this)" style="width: 70px;">
                    <span class="badge bg-success fs-6">₱${option.dataset.price}</span>
                    <button type="button" class="btn-close" onclick="removePart(this)"></button>
                    <input type="hidden" name="parts[]" value="${select.value}:1">
                </div>
            `;
            list.appendChild(li);
            select.value = '';
            updatePartsJson();
        }
    }
    
    function updatePartTotal(input) {
        const li = input.closest('li');
        const hidden = li.querySelector('input[type="hidden"]');
        const price = li.querySelector('.badge').textContent.replace('₱', '');
        const qty = input.value;
        hidden.value = hidden.value.split(':')[0] + ':' + qty;
        updatePartsJson();
    }
    
    function removePart(btn) {
        btn.closest('li').remove();
        updatePartsJson();
    }
    
    function updatePartsJson() {
        const partsField = document.getElementById('parts_json');
        const partsInputs = document.querySelectorAll('input[name="parts[]"]');
        const partsData = Array.from(partsInputs).map(input => input.value);
        partsField.value = JSON.stringify(partsData);
        if (partsData.length === 0) {
            document.getElementById('parts_list').style.display = 'none';
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const radios = document.querySelectorAll('.customer-type');
        const existingSection = document.getElementById('existing_customer_section');
        const newSection = document.getElementById('new_customer_section');
        
        function toggleSections() {
            const checked = document.querySelector('.customer-type:checked');
            if (checked) {
                if (checked.value === 'existing') {
                    existingSection.style.display = 'block';
                    newSection.style.display = 'none';
                } else if (checked.value === 'new') {
                    existingSection.style.display = 'none';
                    newSection.style.display = 'block';
                }
            }
        }
        
        radios.forEach(radio => radio.addEventListener('change', toggleSections));
        toggleSections();
    });
</script>
@endsection
