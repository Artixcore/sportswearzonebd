<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/admin.css'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            window.showAlert = function(type, title, text, options) {
                var opts = Object.assign({ title: title || '', text: text || '' }, options || {});
                if (type === 'success') opts.icon = 'success';
                else if (type === 'error') opts.icon = 'error';
                else if (type === 'warning') opts.icon = 'warning';
                return Swal.fire(opts);
            };
            window.showConfirm = function(title, text, onConfirm, options) {
                return Swal.fire(Object.assign({
                    title: title || 'Are you sure?',
                    text: text || '',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#6b7280'
                }, options || {})).then(function(result) {
                    if (result.isConfirmed && typeof onConfirm === 'function') onConfirm();
                    return result;
                });
            };
        </script>
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            accent: '#059669',
                            'accent-hover': '#047857'
                        }
                    }
                }
            };
            window.showAlert = function(type, title, text, options) {
                var opts = Object.assign({ title: title || '', text: text || '' }, options || {});
                if (type === 'success') opts.icon = 'success';
                else if (type === 'error') opts.icon = 'error';
                else if (type === 'warning') opts.icon = 'warning';
                return Swal.fire(opts);
            };
            window.showConfirm = function(title, text, onConfirm, options) {
                return Swal.fire(Object.assign({
                    title: title || 'Are you sure?',
                    text: text || '',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#6b7280'
                }, options || {})).then(function(result) {
                    if (result.isConfirmed && typeof onConfirm === 'function') onConfirm();
                    return result;
                });
            };
        </script>
    @endif
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
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.orders.index') }}" id="admin-new-orders-bell" class="relative p-1.5 rounded-md text-slate-600 hover:bg-slate-100 hover:text-slate-900" title="New orders (live)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span id="admin-orders-badge" class="absolute -top-0.5 -right-0.5 min-w-[20px] h-5 flex items-center justify-center rounded-full bg-amber-500 text-white text-xs font-semibold px-1.5" aria-live="polite">0</span>
                    </a>
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
    @if(session('success') || session('error'))
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof showAlert === 'function') {
            @if(session('success'))
            showAlert('success', 'Success', {{ json_encode(session('success')) }});
            @endif
            @if(session('error'))
            showAlert('error', 'Error', {{ json_encode(session('error')) }});
            @endif
        }
    });
    </script>
    @endif
    <script>
    (function() {
        // New orders notification: AJAX polling only, no page reloads. Bell shows live count.
        var newOrdersCheckUrl = {{ Route::has('admin.api.new-orders-check') ? json_encode(route('admin.api.new-orders-check')) : 'null' }};
        var ordersShowUrlBase = {{ json_encode(route('admin.orders.show', ['order' => '__ID__'])) }};
        var lastKnownOrderId = 0;
        var pollIntervalMs = 10000;

        function playBip() {
            try {
                var C = window.AudioContext || window.webkitAudioContext;
                if (!C) return;
                var ctx = new C();
                var play = function(freq, start, duration) {
                    var o = ctx.createOscillator();
                    var g = ctx.createGain();
                    o.connect(g);
                    g.connect(ctx.destination);
                    o.frequency.value = freq;
                    o.type = 'sine';
                    g.gain.setValueAtTime(0.15, start);
                    g.gain.exponentialRampToValueAtTime(0.01, start + duration);
                    o.start(start);
                    o.stop(start + duration);
                };
                play(880, 0, 0.08);
                play(880, 0.12, 0.08);
            } catch (e) {}
        }

        function updateBadge(pendingCount) {
            var badge = document.getElementById('admin-orders-badge');
            if (!badge) return;
            var n = parseInt(pendingCount, 10);
            if (isNaN(n) || n < 0) n = 0;
            badge.textContent = n > 99 ? '99+' : n;
            badge.classList.toggle('bg-amber-500', n === 0);
            badge.classList.toggle('bg-emerald-600', n > 0);
        }

        function poll() {
            if (!newOrdersCheckUrl) return;
            var xhr = new XMLHttpRequest();
            xhr.open('GET', newOrdersCheckUrl, true);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onload = function() {
                if (xhr.status !== 200) return;
                try {
                    var data = JSON.parse(xhr.responseText);
                    var latestId = data.latest_order_id || 0;
                    var pendingCount = data.pending_count != null ? data.pending_count : 0;
                    updateBadge(pendingCount);
                    if (latestId > lastKnownOrderId) {
                        if (lastKnownOrderId > 0) {
                            playBip();
                            var viewUrl = ordersShowUrlBase.replace('__ID__', String(latestId));
                            if (typeof showAlert === 'function') {
                                showAlert('success', 'New order received', 'Order #' + latestId, {
                                    showCancelButton: true,
                                    confirmButtonText: 'View order',
                                    cancelButtonText: 'Dismiss'
                                }).then(function(r) {
                                    if (r.isConfirmed) window.open(viewUrl, '_blank');
                                });
                            } else {
                                if (confirm('New order #' + latestId + ' received. View order?')) window.open(viewUrl, '_blank');
                            }
                        }
                        lastKnownOrderId = latestId;
                    }
                } catch (e) {}
            };
            xhr.onerror = function() {};
            xhr.send();
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (newOrdersCheckUrl) {
                poll();
                setInterval(poll, pollIntervalMs);
            }
        });
    })();
    </script>
    @stack('scripts')
</body>
</html>
