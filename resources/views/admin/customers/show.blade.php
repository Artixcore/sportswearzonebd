@extends('layouts.admin')

@section('title', $customer->name)

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-semibold text-slate-800">{{ $customer->name }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.customers.edit', $customer) }}" class="inline-flex px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Edit</a>
        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Delete this customer?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200">Delete</button>
        </form>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
        <h2 class="font-medium text-slate-800 mb-3">Contact</h2>
        <p class="text-sm"><span class="text-slate-500">Email:</span> {{ $customer->email ?? '—' }}</p>
        <p class="text-sm"><span class="text-slate-500">Phone:</span> {{ $customer->phone ?? '—' }}</p>
        @if($customer->notes)
            <p class="text-sm mt-2"><span class="text-slate-500">Notes:</span> {{ $customer->notes }}</p>
        @endif
    </div>
    <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
        <h2 class="font-medium text-slate-800 mb-3">Addresses</h2>
        @forelse($customer->addresses as $addr)
            <p class="text-sm">{{ $addr->address_line1 }}, {{ $addr->city }} {{ $addr->state }} {{ $addr->postal_code }}</p>
        @empty
            <p class="text-sm text-slate-500">No addresses saved.</p>
        @endforelse
    </div>
</div>
<div class="mt-6 bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 font-medium">Orders (online)</div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">ID</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Total</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Status</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Date</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-4 py-2"><a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-600 hover:underline">#{{ $order->id }}</a></td>
                        <td class="px-4 py-2 text-right">৳{{ number_format($order->total, 0) }}</td>
                        <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-700">{{ $order->status }}</span></td>
                        <td class="px-4 py-2 text-sm text-slate-500">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-2"><a href="{{ route('admin.orders.show', $order) }}" class="text-sm text-slate-600">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">No orders.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-slate-200">{{ $orders->links() }}</div>
</div>
@if($sales->isNotEmpty())
<div class="mt-6 bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 font-medium">POS Sales</div>
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">ID</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Total</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Date</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @foreach($sales as $s)
                <tr>
                    <td class="px-4 py-2">#{{ $s->id }}</td>
                    <td class="px-4 py-2 text-right">৳{{ number_format($s->total, 0) }}</td>
                    <td class="px-4 py-2 text-sm">{{ $s->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-2"><a href="{{ route('admin.sales.show', $s) }}" class="text-sm text-slate-600">View</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
