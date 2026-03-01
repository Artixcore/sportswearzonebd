@extends('layouts.app')

@section('title', isset($category) ? $category->name . ' - Shop' : 'Shop - ' . config('app.name'))
@section('meta_description', isset($category) && $category->meta_description ? $category->meta_description : config('seo.default_description', ''))

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-8 lg:flex-row">
        {{-- Sidebar filters --}}
        <aside class="lg:w-64 lg:shrink-0">
            <div class="sticky top-24 space-y-6 rounded-xl border border-gray-200 bg-surface p-4 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Filters</h3>

                {{-- Category --}}
                <div>
                    <h4 class="mb-2 text-xs font-semibold uppercase text-gray-500">Category</h4>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('shop.index') }}{{ request()->except('category') ? '?' . http_build_query(request()->except('category')) : '' }}" class="block rounded-lg px-3 py-2 text-sm {{ !isset($category) ? 'bg-accent font-medium text-white' : 'text-gray-700 hover:bg-muted' }}">All</a>
                        </li>
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('shop.category', $cat->slug) }}" class="block rounded-lg px-3 py-2 text-sm {{ isset($category) && $category->id === $cat->id ? 'bg-accent font-medium text-white' : 'text-gray-700 hover:bg-muted' }}">{{ $cat->name }}</a>
                                @if($cat->children->isNotEmpty())
                                    <ul class="ml-3 mt-0.5 space-y-0.5 border-l border-gray-200 pl-2">
                                        @foreach($cat->children as $child)
                                            <li>
                                                <a href="{{ route('shop.category', $child->slug) }}" class="block rounded-lg px-2 py-1.5 text-sm {{ isset($category) && $category->id === $child->id ? 'bg-accent font-medium text-white' : 'text-gray-600 hover:bg-muted' }}">{{ $child->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Price range (UI only / mock) --}}
                <div>
                    <h4 class="mb-2 text-xs font-semibold uppercase text-gray-500">Price</h4>
                    <ul class="space-y-1">
                        <li><a href="{{ route('shop.index', array_merge(request()->query(), ['price_max' => 1000])) }}" class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-muted">Under ৳1,000</a></li>
                        <li><a href="{{ route('shop.index', array_merge(request()->query(), ['price_min' => 1000, 'price_max' => 3000])) }}" class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-muted">৳1,000 – 3,000</a></li>
                        <li><a href="{{ route('shop.index', array_merge(request()->query(), ['price_min' => 3000])) }}" class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-muted">Over ৳3,000</a></li>
                    </ul>
                </div>

                {{-- Size (mock) --}}
                <div>
                    <h4 class="mb-2 text-xs font-semibold uppercase text-gray-500">Size</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['S', 'M', 'L', 'XL'] as $s)
                            <a href="{{ route('shop.index', array_merge(request()->query(), ['size' => $s])) }}" class="rounded border border-gray-300 px-2 py-1 text-xs text-gray-700 hover:border-accent hover:bg-muted">{{ $s }}</a>
                        @endforeach
                    </div>
                </div>

                {{-- Color (mock) --}}
                <div>
                    <h4 class="mb-2 text-xs font-semibold uppercase text-gray-500">Color</h4>
                    <ul class="space-y-1">
                        @foreach(['Black', 'White', 'Navy', 'Gray'] as $c)
                            <li><a href="{{ route('shop.index', array_merge(request()->query(), ['color' => $c])) }}" class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-muted">{{ $c }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Sort --}}
                <div>
                    <h4 class="mb-2 text-xs font-semibold uppercase text-gray-500">Sort</h4>
                    <form method="GET" class="space-y-1">
                        @foreach(request()->except('sort') as $key => $val)
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endforeach
                        <select name="sort" onchange="this.form.submit()" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent">
                            <option value="" {{ request('sort') === null ? 'selected' : '' }}>Default</option>
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Product grid --}}
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ isset($category) ? $category->name : 'All Products' }}</h1>
            <div class="mt-6 grid grid-cols-3 gap-3 md:grid-cols-4 xl:grid-cols-6 md:gap-4">
                @forelse($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <p class="col-span-full py-12 text-center text-gray-500">No products found.</p>
                @endforelse
            </div>
            <div class="mt-8 flex justify-center">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
