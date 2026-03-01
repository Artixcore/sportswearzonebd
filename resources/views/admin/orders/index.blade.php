@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Orders</h1>
<form method="GET" class="mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
        <select name="status" class="rounded border-slate-300 shadow-sm w-36">
            <option value="">All</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded border-slate-300 shadow-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded border-slate-300 shadow-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ID, name, email" class="rounded border-slate-300 shadow-sm w-48">
    </div>
    <button type="submit" class="px-3 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Filter</button>
</form>
<div class="bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">ID</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Customer</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Total</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Payment</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Status</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Date</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @foreach($orders as $order)
                <tr>
                    <td class="px-4 py-2 font-medium"><a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-600 hover:underline">#{{ $order->id }}</a></td>
                    <td class="px-4 py-2 text-sm">{{ $order->customer?->name ?? $order->user?->name ?? $order->guest_email ?? '—' }}</td>
                    <td class="px-4 py-2 text-sm text-right">৳{{ number_format($order->total, 0) }}</td>
                    <td class="px-4 py-2 text-sm">{{ ucfirst($order->payment_method ?? 'cod') }}</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">{{ $order->status }}</span></td>
                    <td class="px-4 py-2 text-sm text-slate-500">{{ $order->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-sm text-slate-600 hover:text-slate-900">View</a>
                        <a href="{{ route('admin.orders.invoice', $order) }}" class="text-sm text-slate-600 hover:text-slate-900 ml-2">Invoice</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-200">{{ $orders->withQueryString()->links() }}</div>
</div>
@endsection
