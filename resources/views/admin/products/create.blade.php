@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Add Product</h1>
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded border-slate-300 shadow-sm">
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Slug</label>
                <input type="text" name="slug" value="{{ old('slug') }}" placeholder="auto-generated if empty" class="w-full rounded border-slate-300 shadow-sm">
                @error('slug')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                <select name="category_id" class="w-full rounded border-slate-300 shadow-sm">
                    <option value="">— None —</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Short Description</label>
                <textarea name="short_description" rows="2" class="w-full rounded border-slate-300 shadow-sm">{{ old('short_description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full rounded border-slate-300 shadow-sm">{{ old('description') }}</textarea>
            </div>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Price *</label>
                <input type="number" step="0.01" name="price" value="{{ old('price') }}" required class="w-full rounded border-slate-300 shadow-sm">
                @error('price')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Compare at price</label>
                <input type="number" step="0.01" name="compare_at_price" value="{{ old('compare_at_price') }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Cost price</label>
                <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price') }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Discount %</label>
                <input type="number" step="0.01" name="discount_percent" value="{{ old('discount_percent') }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">SKU</label>
                <input type="text" name="sku" value="{{ old('sku') }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Stock</label>
                <input type="number" name="stock" value="{{ old('stock', 0) }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Low stock threshold</label>
                <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 5) }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300">
                <label for="is_active" class="text-sm text-slate-700">Active</label>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-slate-300">
                <label for="is_featured" class="text-sm text-slate-700">Featured</label>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Sort order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Images</label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full text-sm">
            </div>
        </div>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Create Product</button>
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Cancel</a>
    </div>
</form>
@endsection
