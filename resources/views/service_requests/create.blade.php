@extends('layouts.app')

@section('title', 'Create Service Request - TeknoHub')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Create New Service Request
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('service-requests.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="device_type" class="form-label">Device Type</label>
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
                        <label for="device_description" class="form-label">Device Description</label>
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
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Request Guidelines
                </h5>
            </div>
            <div class="card-body">
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
@endsection