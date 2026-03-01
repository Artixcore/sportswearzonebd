@extends('layouts.admin')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-semibold text-slate-800">Order #{{ $order->id }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.orders.invoice', $order) }}" class="inline-flex px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300" target="_blank">Print Invoice</a>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 font-medium">Items</div>
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Product</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Price</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Qty</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($order->items as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item->name }}</td>
                        <td class="px-4 py-2 text-right">৳{{ number_format($item->price, 0) }}</td>
                        <td class="px-4 py-2 text-right">{{ $item->quantity }}</td>
                        <td class="px-4 py-2 text-right">৳{{ number_format($item->price * $item->quantity, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
            <h2 class="font-medium text-slate-800 mb-3">Summary</h2>
            <p class="flex justify-between text-sm"><span class="text-slate-600">Subtotal</span>৳{{ number_format($order->subtotal, 0) }}</p>
            <p class="flex justify-between text-sm"><span class="text-slate-600">Shipping</span>৳{{ number_format($order->shipping, 0) }}</p>
            <p class="flex justify-between text-sm font-medium mt-2"><span>Total</span>৳{{ number_format($order->total, 0) }}</p>
            <p class="flex justify-between text-sm mt-1"><span class="text-slate-600">Payment</span>{{ ucfirst($order->payment_method ?? 'cod') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
            <h2 class="font-medium text-slate-800 mb-3">Shipping</h2>
            <p class="text-sm">{{ $order->shipping_name }}</p>
            <p class="text-sm">{{ $order->shipping_phone }}</p>
            <p class="text-sm">{{ $order->shipping_city }}</p>
            <p class="text-sm text-slate-600">{{ $order->shipping_address }}</p>
        </div>
        <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
            <h2 class="font-medium text-slate-800 mb-3">Update Status</h2>
            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                @csrf
                <select name="status" class="w-full rounded border-slate-300 shadow-sm mb-2">
                    @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
