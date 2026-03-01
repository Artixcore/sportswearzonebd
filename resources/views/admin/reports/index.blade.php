@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Sales & Reports</h1>
<form method="GET" class="mb-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Period</label>
        <select name="period" class="rounded border-slate-300 shadow-sm">
            <option value="day" {{ request('period') === 'day' ? 'selected' : '' }}>Day</option>
            <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Week</option>
            <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Month</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Date</label>
        <input type="date" name="date" value="{{ request('date', now()->toDateString()) }}" class="rounded border-slate-300 shadow-sm">
    </div>
    <button type="submit" class="px-3 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Apply</button>
</form>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow border border-slate-200 p-5">
        <p class="text-sm text-slate-500 font-medium">Total Revenue</p>
        <p class="text-2xl font-bold text-slate-800 mt-1">৳{{ number_format($totalRevenue, 0) }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ $start }} to {{ $end }}</p>
    </div>
    <div class="bg-white rounded-lg shadow border border-slate-200 p-5">
        <p class="text-sm text-slate-500 font-medium">Online Orders</p>
        <p class="text-2xl font-bold text-slate-800 mt-1">{{ $ordersCount }}</p>
    </div>
    <div class="bg-white rounded-lg shadow border border-slate-200 p-5">
        <p class="text-sm text-slate-500 font-medium">POS Sales</p>
        <p class="text-2xl font-bold text-slate-800 mt-1">{{ $salesCount }}</p>
    </div>
    <div class="bg-white rounded-lg shadow border border-slate-200 p-5">
        <p class="text-sm text-slate-500 font-medium">Profit (est.)</p>
        <p class="text-2xl font-bold text-slate-800 mt-1">৳{{ number_format($profitEstimate, 0) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
        <h2 class="font-medium text-slate-800 mb-3">Orders by Status</h2>
        <ul class="space-y-2">
            @forelse($ordersSummary as $status => $count)
                <li class="flex justify-between text-sm"><span class="capitalize">{{ $status }}</span><span>{{ $count }}</span></li>
            @empty
                <li class="text-slate-500 text-sm">No orders in period.</li>
            @endforelse
        </ul>
    </div>
    <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
        <h2 class="font-medium text-slate-800 mb-3">Export</h2>
        <p class="text-sm text-slate-600 mb-2">Download sales report as CSV for the selected period or custom range.</p>
        <a href="{{ route('admin.reports.exportCsv') }}?date_from={{ $start }}&date_to={{ $end }}" class="inline-flex px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300 text-sm">Export CSV</a>
    </div>
</div>
@endsection
