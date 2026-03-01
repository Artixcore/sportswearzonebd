<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
    @vite(['resources/css/admin.css'])
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside id="admin-sidebar" class="w-56 bg-slate-800 text-white flex-shrink-0 flex flex-col transition-all duration-200 print:hidden">
            <div class="p-4 border-b border-slate-700">
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold text-white hover:text-slate-200">{{ config('app.name') }} Admin</a>
            </div>
            <nav class="flex-1 overflow-y-auto p-3">
                <ul class="space-y-0.5">
                    <li><a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-700 hover:text-white">Dashboard</a></li>
                    <li><a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-700 hover:text-white">Products</a></li>
                    <li><a href="{{ route('admin.categories.index') }}" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-700 hover:text-white">Categories</a></li>
                    <li><a href="{{ route('admin.inventory.index') }}" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-700 hover:text-white">Inventory</a></li>
                    <li><a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-700 hover:text-white">Orders</a></li>
                    <li><a href="{{ route('admin.pos.index') }}" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-700 hover:text-white">POS</a></li>
                    <li><a href="{{ route('admin.sales.index') }}" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-700 hover:text-white">Sales</a></li>
                    <li><a href="{{ route('admin.customers.index') }}" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-700 hover:text-white">Customers</a></li>
                    <li><a href="{{ route('admin.reports.index') }}" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-700 hover:text-white">Reports</a></li>
                    <li><a href="{{ route('admin.activity-logs.index') }}" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-700 hover:text-white">Activity Log</a></li>
                    <li><a href="{{ route('admin.settings.index') }}" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-700 hover:text-white">Settings</a></li>
                    <li class="border-t border-slate-700 mt-3 pt-3"><a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-slate-300 hover:bg-slate-700 hover:text-white">View Store</a></li>
                </ul>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            {{-- Header --}}
            <header class="bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between print:hidden">
                <span class="text-slate-500 text-sm">Admin</span>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-slate-600 hover:text-slate-900">Logout</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-4 md:p-6">
                {{-- Toasts --}}
                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 flex items-center justify-between" role="alert">
                        <span>{{ session('success') }}</span>
                        <button type="button" class="text-emerald-600 hover:text-emerald-800" onclick="this.parentElement.remove()">×</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 flex items-center justify-between" role="alert">
                        <span>{{ session('error') }}</span>
                        <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">×</button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
