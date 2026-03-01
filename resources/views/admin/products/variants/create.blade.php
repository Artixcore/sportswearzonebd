@extends('layouts.admin')

@section('title', 'Add Variant')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.products.variants.index', $product) }}" class="text-sm text-slate-600 hover:text-slate-900">← Back to variants</a>
</div>
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Add Variant: {{ $product->name }}</h1>
<form action="{{ route('admin.products.variants.store', $product) }}" method="POST" class="max-w-md space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Name *</label>
        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. M / Red" required class="w-full rounded border-slate-300 shadow-sm">
        @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Size</label>
            <input type="text" name="size" value="{{ old('size') }}" class="w-full rounded border-slate-300 shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Color</label>
            <input type="text" name="color" value="{{ old('color') }}" class="w-full rounded border-slate-300 shadow-sm">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">SKU *</label>
        <input type="text" name="sku" value="{{ old('sku') }}" required class="w-full rounded border-slate-300 shadow-sm">
        @error('sku')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Price adjustment</label>
        <input type="number" step="0.01" name="price_adjustment" value="{{ old('price_adjustment', 0) }}" class="w-full rounded border-slate-300 shadow-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Stock *</label>
        <input type="number" name="stock" value="{{ old('stock', 0) }}" required class="w-full rounded border-slate-300 shadow-sm">
        @error('stock')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Low stock threshold</label>
        <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 5) }}" class="w-full rounded border-slate-300 shadow-sm">
    </div>
    <div class="flex gap-3">
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Create Variant</button>
        <a href="{{ route('admin.products.variants.index', $product) }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Cancel</a>
    </div>
</form>
@endsection
