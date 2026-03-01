@extends('layouts.admin')

@section('title', 'Sale #' . $sale->id)

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-semibold text-slate-800">Sale #{{ $sale->id }}</h1>
    <a href="{{ route('admin.pos.receipt', $sale) }}" class="inline-flex px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300" target="_blank">Print Receipt</a>
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
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Discount</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($sale->items as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item->name }}</td>
                        <td class="px-4 py-2 text-right">৳{{ number_format($item->price, 0) }}</td>
                        <td class="px-4 py-2 text-right">{{ $item->quantity }}</td>
                        <td class="px-4 py-2 text-right">৳{{ number_format($item->discount, 0) }}</td>
                        <td class="px-4 py-2 text-right">৳{{ number_format($item->price * $item->quantity - $item->discount, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
        <p class="flex justify-between text-sm"><span class="text-slate-600">Subtotal</span>৳{{ number_format($sale->subtotal, 0) }}</p>
        <p class="flex justify-between text-sm"><span class="text-slate-600">Discount</span>৳{{ number_format($sale->discount, 0) }}</p>
        <p class="flex justify-between font-medium mt-2"><span>Total</span>৳{{ number_format($sale->total, 0) }}</p>
        <p class="flex justify-between text-sm mt-2"><span class="text-slate-600">Payment</span>{{ ucfirst($sale->payment_method) }}</p>
        @if($sale->customer)
            <p class="flex justify-between text-sm mt-2"><span class="text-slate-600">Customer</span>{{ $sale->customer->name }}</p>
        @endif
        <p class="text-sm text-slate-500 mt-2">By {{ $sale->user->name }} · {{ $sale->created_at->format('M d, Y H:i') }}</p>
    </div>
</div>
@endsection
