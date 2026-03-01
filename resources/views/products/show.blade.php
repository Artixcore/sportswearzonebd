@extends('layouts.app')

@section('title', $product->meta_title ?? $product->name . ' - ' . config('app.name'))
@section('meta_description', $product->meta_description ?? Str::limit($product->short_description ?? $product->description ?? '', 160))

@push('meta')
@if($product->meta_keywords)
<meta name="keywords" content="{{ $product->meta_keywords }}">
@endif
<meta property="og:title" content="{{ $product->meta_title ?? $product->name }}">
<meta property="og:description" content="{{ $product->meta_description ?? Str::limit($product->short_description ?? $product->description ?? '', 200) }}">
<meta property="og:type" content="product">
<meta property="og:url" content="{{ url()->current() }}">
@if($product->primaryImage)
<meta property="og:image" content="{{ asset('storage/' . $product->primaryImage->path) }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $product->meta_title ?? $product->name }}">
@endpush

@push('json-ld')
<script type="application/ld+json">@json((new \App\Services\SeoService())->productJsonLd($product))</script>
<script type="application/ld+json">@json($breadcrumbJsonLd)</script>
@endpush

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
            <li><a href="{{ route('home') }}" class="hover:text-accent">Home</a></li>
            <li>/</li>
            <li><a href="{{ route('shop.index') }}" class="hover:text-accent">Shop</a></li>
            <li>/</li>
            <li class="text-gray-900" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="grid gap-8 lg:grid-cols-2">
        {{-- Image gallery --}}
        <div class="space-y-3">
            <div id="product-main-image" class="aspect-square overflow-hidden rounded-xl bg-muted">
                @if($product->display_images->isNotEmpty())
                    @foreach($product->display_images as $i => $img)
                        <img data-gallery-main="{{ $i }}" src="{{ asset('storage/' . $img->path) }}" alt="{{ $product->name }}" class="h-full w-full object-contain {{ $i === 0 ? '' : 'hidden' }}">
                    @endforeach
                @else
                    <div class="flex h-full w-full items-center justify-center text-gray-400">
                        <svg class="h-24 w-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>
                    </div>
                @endif
            </div>
            @if($product->display_images->count() > 1)
                <div class="flex gap-2 overflow-x-auto pb-2">
                    @foreach($product->display_images as $i => $img)
                        <button type="button" data-gallery-thumb="{{ $i }}" class="gallery-thumb shrink-0 rounded-lg border-2 border-transparent focus:border-accent focus:outline-none">
                            <img src="{{ asset('storage/' . $img->path) }}" alt="" class="h-16 w-16 rounded-lg object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div>
            <span class="inline-block rounded-md bg-base px-2 py-0.5 text-xs font-medium text-white">
                @if($product->category)
                    {{ $product->category->parent_id && $product->category->parent ? $product->category->parent->name . ' › ' . $product->category->name : $product->category->name }}
                @else
                    Uncategorized
                @endif
            </span>
            <h1 class="mt-2 text-2xl font-bold text-gray-900 sm:text-3xl">{{ $product->name }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                @if($product->has_discount)
                    <span class="text-lg text-gray-500 line-through">৳{{ number_format($product->original_price, 0) }}</span>
                    <span class="text-2xl font-bold text-gray-900">৳{{ number_format($product->final_price, 0) }}</span>
                    @if($product->discount_label)
                        <span class="rounded bg-red-100 px-2 py-0.5 text-sm font-medium text-red-700">{{ $product->discount_label }}</span>
                    @endif
                @else
                    <span class="text-2xl font-bold text-gray-900">৳{{ number_format($product->final_price, 0) }}</span>
                @endif
            </div>
            <p class="mt-2 text-sm {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $product->stock > 0 ? 'In stock' : 'Out of stock' }}
            </p>
            @if($product->sku)
                <p class="mt-1 text-sm text-gray-500">SKU: {{ $product->sku }}</p>
            @endif
            @if($product->short_description)
                <p class="mt-3 text-gray-600">{{ $product->short_description }}</p>
            @endif

            <form id="product-cart-form" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                {{-- Size --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Size <span class="text-red-500">*</span></label>
                    <div class="mt-1 flex flex-wrap gap-2">
                        @foreach($allowedSizes as $s)
                            <label class="cursor-pointer rounded-lg border border-gray-300 px-3 py-2 text-sm hover:border-accent has-[:checked]:border-accent has-[:checked]:bg-accent has-[:checked]:text-white">
                                <input type="radio" name="size" value="{{ $s }}" class="sr-only" {{ $loop->first ? 'checked' : '' }} required>
                                {{ $s }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <div>
                        <label for="qty" class="sr-only">Quantity</label>
                        <input type="number" id="qty" name="quantity" value="1" min="1" max="{{ max(1, $product->stock) }}" class="w-20 rounded-lg border border-gray-300 px-3 py-2 text-center focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent">
                    </div>
                    <div class="flex gap-2">
                        <button type="button" id="btn-add-cart" {{ $product->stock < 1 ? 'disabled' : '' }} class="rounded-lg bg-accent px-6 py-2.5 font-medium text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 disabled:opacity-50">
                            Add to cart
                        </button>
                        <button type="button" id="btn-buy-now" {{ $product->stock < 1 ? 'disabled' : '' }} class="rounded-lg border-2 border-accent bg-transparent px-6 py-2.5 font-medium text-accent hover:bg-accent hover:text-white focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 disabled:opacity-50">
                            Buy now
                        </button>
                    </div>
                </div>
            </form>

            {{-- Tabs: Description, Reviews, Shipping --}}
            <div class="mt-10" id="product-tabs">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex gap-6" aria-label="Tabs">
                        <button type="button" data-tab="description" class="product-tab border-b-2 border-accent py-3 text-sm font-medium text-accent">Description</button>
                        <button type="button" data-tab="reviews" class="product-tab border-b-2 border-transparent py-3 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">Reviews</button>
                        <button type="button" data-tab="shipping" class="product-tab border-b-2 border-transparent py-3 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">Shipping</button>
                    </nav>
                </div>
                <div class="py-4">
                    <div id="tab-description" class="product-tab-pane prose prose-sm max-w-none text-gray-600">
                        @if($product->description)
                            {!! nl2br(e($product->description)) !!}
                        @else
                            <p>No description available.</p>
                        @endif
                    </div>
                    <div id="tab-reviews" class="product-tab-pane hidden">
                        <p class="text-gray-500">No reviews yet. Be the first to review!</p>
                    </div>
                    <div id="tab-shipping" class="product-tab-pane hidden text-gray-600">
                        <p>Free delivery on orders over ৳1,000. Standard delivery 2–5 business days. Easy 30-day returns.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($related->isNotEmpty())
        <section class="mt-16 border-t border-gray-200 pt-12">
            <h2 class="text-xl font-bold text-gray-900">Related Products</h2>
            <div class="mt-6 grid grid-cols-3 gap-3 md:grid-cols-4 xl:grid-cols-6 md:gap-4">
                @foreach($related as $p)
                    <x-product-card :product="$p" />
                @endforeach
            </div>
        </section>
    @endif
</div>

@if($product->display_images->count() > 1)
<script>
document.addEventListener('DOMContentLoaded', function() {
    var thumbs = document.querySelectorAll('[data-gallery-thumb]');
    var mains = document.querySelectorAll('[data-gallery-main]');
    thumbs.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var i = this.getAttribute('data-gallery-thumb');
            mains.forEach(function(img) { img.classList.add('hidden'); });
            var main = document.querySelector('[data-gallery-main="' + i + '"]');
            if (main) main.classList.remove('hidden');
            thumbs.forEach(function(t) { t.classList.remove('border-accent'); });
            this.classList.add('border-accent');
        });
    });
    if (thumbs.length) thumbs[0].classList.add('border-accent');
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('product-tabs');
    if (!container) return;
    container.querySelectorAll('.product-tab').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tab = this.getAttribute('data-tab');
            container.querySelectorAll('.product-tab').forEach(function(b) {
                b.classList.remove('border-accent', 'text-accent');
                b.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-accent', 'text-accent');
            container.querySelectorAll('.product-tab-pane').forEach(function(p) {
                p.classList.add('hidden');
            });
            var pane = document.getElementById('tab-' + tab);
            if (pane) pane.classList.remove('hidden');
        });
    });
});
</script>
@push('scripts')
<script>
(function() {
    var addUrl = @json(route('cart.add'));
    var checkoutUrl = @json(route('checkout.index'));
    $(function() {
        $('#btn-add-cart').on('click', function() {
            var btn = $(this);
            if (btn.prop('disabled')) return;
            var form = $('#product-cart-form');
            var data = form.serialize();
            btn.prop('disabled', true).addClass('opacity-75');
            $.ajax({
                url: addUrl,
                method: 'POST',
                data: data,
                dataType: 'json'
            }).done(function(res) {
                if (typeof updateNavCartCount === 'function') updateNavCartCount(res.cart_count);
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'success', title: 'Added to cart', text: res.message || 'Added to cart.' });
            }).fail(function(xhr) {
                var msg = 'Could not add to cart.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var first = Object.values(xhr.responseJSON.errors)[0];
                    if (Array.isArray(first)) msg = first[0];
                } else if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }).always(function() {
                btn.prop('disabled', false).removeClass('opacity-75');
            });
        });
        $('#btn-buy-now').on('click', function() {
            var btn = $(this);
            if (btn.prop('disabled')) return;
            var form = $('#product-cart-form');
            var data = form.serialize() + '&redirect=checkout';
            btn.prop('disabled', true).addClass('opacity-75');
            $.ajax({
                url: addUrl,
                method: 'POST',
                data: data,
                dataType: 'json'
            }).done(function(res) {
                if (typeof updateNavCartCount === 'function') updateNavCartCount(res.cart_count);
                if (res.redirect) {
                    window.location.href = res.redirect;
                    return;
                }
                window.location.href = checkoutUrl;
            }).fail(function(xhr) {
                var msg = 'Could not add to cart.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var first = Object.values(xhr.responseJSON.errors)[0];
                    if (Array.isArray(first)) msg = first[0];
                } else if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: msg });
                btn.prop('disabled', false).removeClass('opacity-75');
            });
        });
    });
})();
</script>
@endpush
@endsection

@if(isset($viewContentEventId) && $metaPixelId = \App\Models\Setting::get('meta_pixel_id', config('meta.pixel_id')))
@push('scripts')
<script>
    if (typeof fbq !== 'undefined') {
        fbq('track', 'ViewContent', {
            content_type: 'product',
            content_ids: ['{{ $product->id }}'],
            content_name: @json($product->name),
            value: {{ $product->price }},
            currency: 'BDT'
        }, { eventID: '{{ $viewContentEventId }}' });
    }
</script>
@endpush
@endif
