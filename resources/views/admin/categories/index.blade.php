@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-semibold text-slate-800">Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Add Category</a>
</div>
<div class="bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Name</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Slug</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Parent</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Sort</th>
                <th class="px-4 py-2 w-32"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @foreach($categories as $c)
                <tr>
                    <td class="px-4 py-2 font-medium text-slate-800">{{ $c->name }}</td>
                    <td class="px-4 py-2 text-sm text-slate-600">{{ $c->slug }}</td>
                    <td class="px-4 py-2 text-sm">{{ $c->parent?->name ?? '—' }}</td>
                    <td class="px-4 py-2 text-sm">{{ $c->sort_order }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.categories.edit', $c) }}" class="text-sm text-slate-600 hover:text-slate-900">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $c) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-200">{{ $categories->links() }}</div>
</div>
@endsection
