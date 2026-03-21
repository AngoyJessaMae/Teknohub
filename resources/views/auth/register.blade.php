@extends('layouts.app')

@section('title', 'Register - TeknoHub')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h2 class="text-primary mb-1">
                        <i class="fas fa-tools me-2"></i>TeknoHub
                    </h2>
                    <p class="text-muted">Create Your Account</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="full_name" class="form-label text-main">Full Name</label>
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                            id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                        @error('full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label text-main">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="contact_number" class="form-label text-main">Contact Number</label>
                        <input type="text" class="form-control @error('contact_number') is-invalid @enderror"
                            id="contact_number" name="contact_number" value="{{ old('contact_number') }}" required>
                        @error('contact_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label text-main">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label text-main">Confirm Password</label>
                        <input type="password" class="form-control"
                            id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label text-main">Register As</label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="Customer" selected>Customer</option>
                        </select>
                        @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <p class="text-muted mb-0">Already have an account?</p>
                    <a href="{{ route('login') }}" class="text-secondary text-decoration-none">
                        Login Here
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection