@extends('layouts.admin')

@section('title', 'Inventory')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-semibold text-slate-800">Inventory Logs</h1>
    <a href="{{ route('admin.inventory.adjust') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Adjust Stock</a>
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Product</label>
        <select name="product_id" class="rounded border-slate-300 shadow-sm w-48">
            <option value="">All</option>
            @foreach($products as $p)
                <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Type</label>
        <select name="type" class="rounded border-slate-300 shadow-sm w-32">
            <option value="">All</option>
            <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>In</option>
            <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Out</option>
            <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
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
    <button type="submit" class="px-3 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Filter</button>
</form>

<div class="bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Date</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Product</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Variant</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Type</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Quantity</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">By</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Notes</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($logs as $log)
                <tr>
                    <td class="px-4 py-2 text-sm text-slate-600">{{ $log->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-2 text-sm font-medium"><a href="{{ route('admin.inventory.productHistory', $log->product) }}" class="text-emerald-600 hover:underline">{{ $log->product->name }}</a></td>
                    <td class="px-4 py-2 text-sm">{{ $log->productVariant?->name ?? '—' }}</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs font-medium @if($log->type === 'in') bg-emerald-100 text-emerald-800 @elseif($log->type === 'out') bg-red-100 text-red-800 @else bg-slate-100 text-slate-700 @endif">{{ $log->type }}</span></td>
                    <td class="px-4 py-2 text-sm text-right {{ $log->quantity >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $log->quantity >= 0 ? '+' : '' }}{{ $log->quantity }}</td>
                    <td class="px-4 py-2 text-sm">{{ $log->user?->name ?? '—' }}</td>
                    <td class="px-4 py-2 text-sm text-slate-500">{{ Str::limit($log->notes, 30) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">No inventory logs.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-200">{{ $logs->withQueryString()->links() }}</div>
</div>
@endsection
