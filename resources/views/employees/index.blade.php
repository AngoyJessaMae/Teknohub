@extends('layouts.app')

@section('title', 'Manage Employees - TeknoHub')

@section('content')
<div class="card">
    <div class="card-header text-main">
        <h5 class="mb-0">
            <i class="fas fa-users-cog me-2"></i>Manage Employees
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Job Title</th>
                        <th>Skills</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td>{{ $employee->user->full_name }}</td>
                        <td>{{ $employee->user->email }}</td>
                        <td>{{ $employee->job_title }}</td>
                        <td>{{ $employee->skills }}</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No employees found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection