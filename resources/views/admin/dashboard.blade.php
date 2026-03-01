@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Dashboard</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow border border-slate-200 p-5">
        <p class="text-sm text-slate-500 font-medium">Today's Orders</p>
        <p class="text-2xl font-bold text-slate-800 mt-1">{{ $todayOrdersCount }}</p>
    </div>
    <div class="bg-white rounded-lg shadow border border-slate-200 p-5">
        <p class="text-sm text-slate-500 font-medium">Today's Sales</p>
        <p class="text-2xl font-bold text-slate-800 mt-1">৳{{ number_format($todaySalesTotal, 0) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow border border-slate-200 p-5">
        <p class="text-sm text-slate-500 font-medium">Low Stock Alerts</p>
        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $lowStockCount }}</p>
    </div>
    <div class="bg-white rounded-lg shadow border border-slate-200 p-5">
        <p class="text-sm text-slate-500 font-medium">Pending Orders</p>
        <p class="text-2xl font-bold text-slate-800 mt-1">{{ $pendingOrdersCount }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200">
            <h2 class="font-semibold text-slate-800">Top Selling Products</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Product</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Qty Sold</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($topSellingProducts as $item)
                        <tr>
                            <td class="px-4 py-2 text-sm text-slate-800">{{ $item->product?->name ?? '—' }}</td>
                            <td class="px-4 py-2 text-sm text-right text-slate-600">{{ $item->total_qty }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-4 py-4 text-sm text-slate-500 text-center">No data yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200">
            <h2 class="font-semibold text-slate-800">Recent Activity</h2>
        </div>
        <ul class="divide-y divide-slate-200 max-h-80 overflow-y-auto">
            @forelse($recentActivity as $log)
                <li class="px-4 py-3 text-sm">
                    <span class="text-slate-600">{{ $log->description }}</span>
                    <span class="text-slate-400"> — {{ $log->user?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</span>
                </li>
            @empty
                <li class="px-4 py-4 text-slate-500">No recent activity.</li>
            @endforelse
        </ul>
    </div>
</div>

<div class="mt-6 bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200">
        <h2 class="font-semibold text-slate-800">Recent Orders</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">ID</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Customer</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Total</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Status</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Date</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($recentOrders as $order)
                    <tr>
                        <td class="px-4 py-2"><a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-600 hover:underline">#{{ $order->id }}</a></td>
                        <td class="px-4 py-2 text-sm">{{ $order->customer?->name ?? $order->user?->name ?? $order->guest_email ?? '—' }}</td>
                        <td class="px-4 py-2 text-sm text-right">৳{{ number_format($order->total, 0) }}</td>
                        <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">{{ $order->status }}</span></td>
                        <td class="px-4 py-2 text-sm text-slate-500">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-2"><a href="{{ route('admin.orders.show', $order) }}" class="text-sm text-slate-600 hover:text-slate-900">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
