@extends('layouts.admin')

@section('title', 'Variants: ' . $product->name)

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <a href="{{ route('admin.products.edit', $product) }}" class="text-sm text-slate-600 hover:text-slate-900 mb-1 inline-block">← Back to product</a>
        <h1 class="text-2xl font-semibold text-slate-800">Variants: {{ $product->name }}</h1>
    </div>
    <a href="{{ route('admin.products.variants.create', $product) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Add Variant</a>
</div>

<div class="bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Name</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Size / Color</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">SKU</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Price adj.</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Stock</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Low threshold</th>
                <th class="px-4 py-2 w-24"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($product->variants as $v)
                <tr>
                    <td class="px-4 py-2 font-medium text-slate-800">{{ $v->name }}</td>
                    <td class="px-4 py-2 text-sm text-slate-600">{{ $v->size }} / {{ $v->color }}</td>
                    <td class="px-4 py-2 text-sm">{{ $v->sku }}</td>
                    <td class="px-4 py-2 text-sm text-right">৳{{ number_format($v->price_adjustment, 0) }}</td>
                    <td class="px-4 py-2 text-sm text-right">{{ $v->stock }} @if($v->isLowStock())<span class="text-amber-600">(low)</span>@endif</td>
                    <td class="px-4 py-2 text-sm text-right">{{ $v->low_stock_threshold }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.products.variants.edit', [$product, $v]) }}" class="text-sm text-slate-600 hover:text-slate-900">Edit</a>
                        <form action="{{ route('admin.products.variants.destroy', [$product, $v]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this variant?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">No variants yet. Add size/color variants for this product.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
