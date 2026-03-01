@extends('layouts.store')

@section('title', isset($category) ? $category->name . ' - Shop' : 'Shop - ' . config('app.name'))
@section('meta_description', isset($category) && $category->meta_description ? $category->meta_description : config('seo.default_description'))

@section('content')
<div class="container py-4">
    <div class="row">
        <aside class="col-lg-3 mb-4">
            <h6 class="fw-bold">Categories</h6>
            <ul class="list-group list-group-flush">
                <li class="list-group-item px-0">
                    <a href="{{ route('shop.index') }}" class="text-decoration-none {{ !isset($category) ? 'fw-bold' : '' }}">All</a>
                </li>
                @foreach($categories as $cat)
                    <li class="list-group-item px-0">
                        <a href="{{ route('shop.category', $cat->slug) }}" class="text-decoration-none {{ isset($category) && $category->id === $cat->id ? 'fw-bold' : '' }}">{{ $cat->name }}</a>
                    </li>
                @endforeach
            </ul>
            <form method="GET" class="mt-3">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <label class="form-label small">Sort</label>
                <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Default</option>
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
            </form>
        </aside>
        <div class="col-lg-9">
            <h1 class="h4 mb-4">{{ isset($category) ? $category->name : 'All Products' }}</h1>
            <div class="row g-4">
                @forelse($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @empty
                    <div class="col-12 text-center py-5 text-muted">No products found.</div>
                @endforelse
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
