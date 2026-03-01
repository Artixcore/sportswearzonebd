@extends('layouts.app')

@section('title', config('seo.default_title', config('app.name')))
@section('meta_description', config('seo.default_description', ''))

@push('json-ld')
<script type="application/ld+json">@json((new \App\Services\SeoService())->websiteJsonLd())</script>
<script type="application/ld+json">@json((new \App\Services\SeoService())->organizationJsonLd())</script>
@endpush

@section('content')
{{-- Hero banner --}}
<section class="relative overflow-hidden bg-base">
    <div class="absolute inset-0 bg-gradient-to-br from-base via-base-light to-accent/20"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">Men's Fashion & Sportswear</h1>
            <p class="mx-auto mt-4 max-w-xl text-lg text-gray-300">Premium style for the modern man. Sports, Panjabi, Attar & more.</p>
            <a href="{{ route('shop.index') }}" class="mt-8 inline-flex items-center justify-center rounded-lg bg-accent px-6 py-3 text-base font-semibold text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-base">
                Shop Now
            </a>
        </div>
    </div>
</section>

{{-- Category chips --}}
@php
    $chipCategories = isset($categories) && $categories->isNotEmpty()
        ? $categories->take(6)
        : collect([
            (object)['name' => 'Sports', 'slug' => 'sports'],
            (object)['name' => 'Panjabi', 'slug' => 'panjabi'],
            (object)['name' => 'Attar', 'slug' => 'attar'],
            (object)['name' => 'Tupi', 'slug' => 'tupi'],
            (object)['name' => 'T-shirts', 'slug' => 't-shirts'],
            (object)['name' => 'Pants', 'slug' => 'pants'],
        ]);
@endphp
<section class="border-b border-muted-border bg-surface py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3">
            @foreach($chipCategories as $cat)
                <a href="{{ isset($cat->id) ? route('shop.category', $cat->slug) : route('shop.index') }}"
                   class="rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:border-accent hover:bg-accent hover:text-white focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured products --}}
<section class="py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900">Featured Products</h2>
        <div class="mt-6 grid grid-cols-3 gap-3 md:grid-cols-4 xl:grid-cols-6 md:gap-4">
            @forelse($featuredProducts ?? [] as $product)
                <x-product-card :product="$product" />
            @empty
                <p class="col-span-full text-center text-gray-500">No featured products yet.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- Promo strip --}}
<section class="bg-accent py-4">
    <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
        <p class="text-sm font-medium text-white sm:text-base">Free delivery on orders over ৳1,000 &bull; 30-day easy returns &bull; Ramadan offers available</p>
    </div>
</section>

{{-- New arrivals (optional) --}}
@if(isset($newArrivals) && $newArrivals->isNotEmpty())
<section class="py-12 bg-muted/50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900">New Arrivals</h2>
        <div class="mt-6 grid grid-cols-3 gap-3 md:grid-cols-4 xl:grid-cols-6 md:gap-4">
            @foreach($newArrivals as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
