@extends('layouts.admin')

@section('title', 'Sales')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">POS Sales</h1>
<form method="GET" class="mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded border-slate-300 shadow-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded border-slate-300 shadow-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Payment</label>
        <select name="payment_method" class="rounded border-slate-300 shadow-sm">
            <option value="">All</option>
            <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Card</option>
            <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>COD</option>
        </select>
    </div>
    <button type="submit" class="px-3 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Filter</button>
</form>
<div class="bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">ID</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Date</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Customer</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Total</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Payment</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @foreach($sales as $sale)
                <tr>
                    <td class="px-4 py-2 font-medium">#{{ $sale->id }}</td>
                    <td class="px-4 py-2 text-sm">{{ $sale->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-2 text-sm">{{ $sale->customer?->name ?? 'Walk-in' }}</td>
                    <td class="px-4 py-2 text-sm text-right">৳{{ number_format($sale->total, 0) }}</td>
                    <td class="px-4 py-2 text-sm">{{ ucfirst($sale->payment_method) }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.sales.show', $sale) }}" class="text-sm text-slate-600 hover:text-slate-900">View</a>
                        <a href="{{ route('admin.pos.receipt', $sale) }}" class="text-sm text-slate-600 hover:text-slate-900 ml-2">Receipt</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-200">{{ $sales->withQueryString()->links() }}</div>
</div>
@endsection
