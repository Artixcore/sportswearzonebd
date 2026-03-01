@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Add Category</a>
</div>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Parent</th>
                <th>Sort</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->slug }}</td>
                    <td>{{ $c->parent?->name ?? '—' }}</td>
                    <td>{{ $c->sort_order }}</td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $categories->links() }}
@endsection
