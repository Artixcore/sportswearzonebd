@extends('layouts.admin')

@section('title', 'Activity Log')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Activity Log</h1>
<form method="GET" class="mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">User</label>
        <select name="user_id" class="rounded border-slate-300 shadow-sm w-40">
            <option value="">All</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Action</label>
        <input type="text" name="action" value="{{ request('action') }}" placeholder="e.g. product" class="rounded border-slate-300 shadow-sm w-32">
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
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">User</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Action</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Description</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($logs as $log)
                <tr>
                    <td class="px-4 py-2 text-sm text-slate-600">{{ $log->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-2 text-sm">{{ $log->user?->name ?? '—' }}</td>
                    <td class="px-4 py-2 text-sm font-medium">{{ $log->action }}</td>
                    <td class="px-4 py-2 text-sm text-slate-600">{{ Str::limit($log->description, 60) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No activity logs.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-200">{{ $logs->withQueryString()->links() }}</div>
</div>
@endsection
