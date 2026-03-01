@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Edit Category</h1>
<form action="{{ route('admin.categories.update', $category) }}" method="POST" class="max-w-md space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Name *</label>
        <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full rounded border-slate-300 shadow-sm">
        @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" class="w-full rounded border-slate-300 shadow-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Parent</label>
        <select name="parent_id" class="w-full rounded border-slate-300 shadow-sm">
            <option value="">— None —</option>
            @foreach($parents as $p)
                <option value="{{ $p->id }}" {{ old('parent_id', $category->parent_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Sort order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="w-full rounded border-slate-300 shadow-sm">
    </div>
    <div class="flex gap-3">
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Update Category</button>
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Cancel</a>
    </div>
</form>
@endsection
