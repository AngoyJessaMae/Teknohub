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
                            id="device_description" name="device_description" rows="4"
                            placeholder="Please describe the issue with your device..." required>{{ old('device_description') }}</textarea>
                        @error('device_description')
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
    document.addEventListener('DOMContentLoaded', function() {
        var radios = document.querySelectorAll('.customer-type');
        var existingSection = document.getElementById('existing_customer_section');
        var newSection = document.getElementById('new_customer_section');
        
        function toggleSections() {
            var checked = document.querySelector('.customer-type:checked');
            if (checked && checked.value === 'existing') {
                existingSection.style.display = 'block';
                newSection.style.display = 'none';
            } else if (checked && checked.value === 'new') {
                existingSection.style.display = 'none';
                newSection.style.display = 'block';
            }
        }
        
        radios.forEach(function(radio) {
            radio.addEventListener('change', toggleSections);
        });
        
        toggleSections();
    });
</script>
@endsection
