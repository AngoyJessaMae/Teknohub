<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TeknoHub - Service Request & Repair Management')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --dark-bg: #1f2937;
            --dark-card: #374151;
            --text-light: #f9fafb;
            --text-muted: #9ca3af;
        }

        body {
            background-color: var(--dark-bg);
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background-color: var(--dark-card);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
        }

        .sidebar .nav-link {
            color: var(--text-muted);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .main-content {
            background-color: var(--dark-bg);
            min-height: 100vh;
        }

        .card {
            background-color: var(--dark-card);
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .table-dark {
            --bs-table-bg: var(--dark-card);
            --bs-table-border-color: #4b5563;
        }

        .form-control,
        .form-select {
            background-color: var(--dark-card);
            border: 1px solid #4b5563;
            color: var(--text-light);
        }

        .form-control:focus,
        .form-select:focus {
            background-color: var(--dark-card);
            border-color: var(--primary-color);
            color: var(--text-light);
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25);
        }

        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.5em 0.75em;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            @auth
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4 class="navbar-brand mb-0">
                            <i class="fas fa-tools me-2"></i>TeknoHub
                        </h4>
                    </div>

                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>

                        @if(auth()->user()->role !== 'employee')
                        <a class="nav-link {{ request()->routeIs('service-requests.*') ? 'active' : '' }}" href="{{ route('service-requests.index') }}">
                            <i class="fas fa-wrench me-2"></i>Service Requests
                        </a>
                        @endif

                        @if(auth()->user()->role === 'admin')
                        <a class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
                            <i class="fas fa-boxes me-2"></i>Inventory
                        </a>

                        <a class="nav-link {{ request()->routeIs('billing.*') ? 'active' : '' }}" href="{{ route('billing.index') }}">
                            <i class="fas fa-file-invoice-dollar me-2"></i>Billing
                        </a>
                        @endif

                        @if(auth()->user()->role === 'employee')
                        <a class="nav-link {{ request()->routeIs('queue.*') ? 'active' : '' }}" href="{{ route('queue.index') }}">
                            <i class="fas fa-list-ol me-2"></i>Service Queue
                        </a>
                        @endif

                        <hr class="text-muted">

                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </nav>

                    <div class="mt-auto pt-3">
                        <div class="text-center text-muted">
                            <small>Logged in as:</small><br>
                            <strong>{{ auth()->user()->full_name }}</strong><br>
                            <span class="badge bg-primary">{{ ucfirst(auth()->user()->role) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endauth

            <div class="col-md-9 col-lg-10 px-4 py-3">
                <div class="main-content">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>