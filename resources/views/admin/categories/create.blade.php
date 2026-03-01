@extends('layouts.admin')

@section('title', 'Add Category')

@section('content')
<h1 class="h4 mb-4">Add Category</h1>
<form action="{{ route('admin.categories.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Name *</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="auto-generated if empty">
    </div>
    <div class="mb-3">
        <label class="form-label">Parent</label>
        <select name="parent_id" class="form-select">
            <option value="">— None —</option>
            @foreach($parents as $p)
                <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Meta Title</label>
        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Meta Description</label>
        <textarea name="meta_description" class="form-control" rows="2">{{ old('meta_description') }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Sort order</label>
        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
    </div>
    <button type="submit" class="btn btn-primary">Create Category</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
