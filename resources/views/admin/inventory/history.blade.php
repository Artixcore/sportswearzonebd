@extends('layouts.admin')

@section('title', 'Inventory History: ' . $product->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.inventory.index') }}" class="text-sm text-slate-600 hover:text-slate-900">← Back to inventory</a>
</div>
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Inventory History: {{ $product->name }}</h1>
<div class="bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Date</th>
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
                    <td class="px-4 py-2 text-sm">{{ $log->productVariant?->name ?? '—' }}</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs font-medium @if($log->type === 'in') bg-emerald-100 text-emerald-800 @elseif($log->type === 'out') bg-red-100 text-red-800 @else bg-slate-100 text-slate-700 @endif">{{ $log->type }}</span></td>
                    <td class="px-4 py-2 text-sm text-right {{ $log->quantity >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $log->quantity >= 0 ? '+' : '' }}{{ $log->quantity }}</td>
                    <td class="px-4 py-2 text-sm">{{ $log->user?->name ?? '—' }}</td>
                    <td class="px-4 py-2 text-sm text-slate-500">{{ Str::limit($log->notes, 40) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No logs for this product.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-200">{{ $logs->links() }}</div>
</div>
@endsection
