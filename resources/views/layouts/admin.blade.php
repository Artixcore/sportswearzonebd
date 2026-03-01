<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/store.css', 'resources/js/store.js'])
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
</head>
<body>
    <div class="d-flex">
        <nav id="admin-sidebar" class="bg-dark text-white vh-100 p-3" style="width: 220px;">
            <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none fw-bold d-block mb-4">{{ config('app.name') }} Admin</a>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.products.index') }}">Products</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.categories.index') }}">Categories</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.orders.index') }}">Orders</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.customers.index') }}">Customers</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.settings.index') }}">Settings</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('home') }}">View Store</a></li>
            </ul>
        </nav>
        <div class="flex-grow-1 d-flex flex-column">
            <header class="bg-light border-bottom px-4 py-2 d-flex justify-content-between align-items-center">
                <span class="text-muted">Admin</span>
                <div>
                    <span class="me-2">{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Logout</button>
                    </form>
                </div>
            </header>
            <main class="p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
    @if (!file_exists(public_path('build/manifest.json')) && !file_exists(public_path('hot')))
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @endif
</body>
</html>
