@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-semibold text-slate-800">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Add Product</a>
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or SKU" class="rounded border-slate-300 shadow-sm w-48">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Category</label>
        <select name="category_id" class="rounded border-slate-300 shadow-sm w-40">
            <option value="">All</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Stock</label>
        <select name="stock_status" class="rounded border-slate-300 shadow-sm w-36">
            <option value="">All</option>
            <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Low stock</option>
            <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In stock</option>
            <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of stock</option>
        </select>
    </div>
    <button type="submit" class="px-3 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Filter</button>
</form>

<div class="bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Image</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">SKU</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Price</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Stock</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Status</th>
                    <th class="px-4 py-2 w-24"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($products as $p)
                    <tr>
                        <td class="px-4 py-2">
                            @if($p->primaryImage)
                                <img src="{{ storage_asset($p->primaryImage->path) }}" alt="" class="w-12 h-12 object-cover rounded">
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.products.edit', $p) }}" class="font-medium text-slate-800 hover:text-emerald-600">{{ $p->name }}</a>
                            @if($p->variants_count > 0)
                                <span class="text-xs text-slate-400">({{ $p->variants_count }} variants)</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm text-slate-600">{{ $p->sku ?? '—' }}</td>
                        <td class="px-4 py-2 text-sm text-right">৳{{ number_format($p->price, 0) }}</td>
                        <td class="px-4 py-2 text-sm text-right">{{ $p->stock }}</td>
                        <td class="px-4 py-2">
                            @if($p->isLowStock())
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">Low stock</span>
                            @endif
                            @if($p->is_active)
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Active</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.products.variants.index', $p) }}" class="text-sm text-slate-600 hover:text-slate-900">Variants</a>
                                <a href="{{ route('admin.products.edit', $p) }}" class="text-sm text-slate-600 hover:text-slate-900">Edit</a>
                                <form action="{{ route('admin.products.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-slate-200">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection
