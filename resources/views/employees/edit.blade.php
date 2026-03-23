@extends('layouts.app')

@section('title', 'Edit Employee - TeknoHub')

@section('content')
<div class="card">
    <div class="card-header text-main d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-user-edit me-2"></i>Edit Employee
        </h5>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Back to list
        </a>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>There were some problems with your input:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('employees.update', $employee) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $employee->user->full_name) }}" placeholder="e.g., John Doe" required>
                    <small class="text-muted">Enter the employee's complete name.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email (username)</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $employee->user->email) }}" placeholder="e.g., john.doe@teknohub.com" required>
                    <small class="text-muted">This is their login username.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $employee->user->contact_number) }}" placeholder="e.g., 0917XXXXXXX" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Department</label>
                    <input type="text" name="department_name" class="form-control" value="{{ old('department_name', $employee->department_name) }}" placeholder="e.g., Hardware Repair" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Job Title</label>
                    <input type="text" name="job_title" class="form-control" value="{{ old('job_title', $employee->job_title) }}" placeholder="e.g., Senior Technician" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Skills (comma-separated)</label>
                    <textarea name="skills" class="form-control" rows="3" placeholder="e.g., diagnostics, soldering, data recovery">{{ old('skills', $employee->skills) }}</textarea>
                    <small class="text-muted">List key skills separated by commas.</small>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Update Employee
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
