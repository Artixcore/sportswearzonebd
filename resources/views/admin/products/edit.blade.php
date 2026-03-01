@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<h1 class="h4 mb-4">Edit Product</h1>
<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label class="form-label">Name *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $product->slug) }}">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">— None —</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Short Description</label>
                <textarea name="short_description" class="form-control" rows="2">{{ old('short_description', $product->short_description) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Price *</label>
                <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product->price) }}" required>
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Compare at price</label>
                <input type="number" step="0.01" name="compare_at_price" class="form-control" value="{{ old('compare_at_price', $product->compare_at_price) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">SKU</label>
                <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}">
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="is_featured" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">Featured</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Sort order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $product->sort_order) }}">
            </div>
            @if($product->images->isNotEmpty())
                <p class="small text-muted">Current images:</p>
                <div class="d-flex gap-1 mb-2 flex-wrap">
                    @foreach($product->images as $img)
                        <img src="{{ asset('storage/' . $img->path) }}" alt="" style="width: 60px; height: 60px; object-fit: cover;">
                    @endforeach
                </div>
            @endif
            <div class="mb-3">
                <label class="form-label">Add more images</label>
                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Update Product</button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
