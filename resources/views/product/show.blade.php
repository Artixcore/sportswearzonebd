@extends('layouts.store')

@section('title', $product->name . ' - ' . config('app.name'))
@section('meta_description', Str::limit($product->short_description ?? $product->description, 160))

@push('meta')
<meta property="og:title" content="{{ $product->name }}">
<meta property="og:description" content="{{ Str::limit($product->short_description ?? $product->description, 200) }}">
<meta property="og:type" content="product">
<meta property="og:url" content="{{ url()->current() }}">
@if($product->primaryImage)
<meta property="og:image" content="{{ asset('storage/' . $product->primaryImage->path) }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $product->name }}">
@endsection

@push('json-ld')
<script type="application/ld+json">@json((new \App\Services\SeoService())->productJsonLd($product))</script>
<script type="application/ld+json">@json((new \App\Services\SeoService())->breadcrumbJsonLd([
    ['name' => 'Home', 'url' => route('home')],
    ['name' => 'Shop', 'url' => route('shop.index')],
    ['name' => $product->name],
]))</script>
@endpush

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Shop</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-lg-6 mb-4">
            @if($product->images->isNotEmpty())
                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($product->images as $i => $img)
                            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $img->path) }}" class="d-block w-100" alt="{{ $product->name }}" style="max-height: 400px; object-fit: contain;">
                            </div>
                        @endforeach
                    </div>
                    @if($product->images->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    @endif
                </div>
            @else
                <div class="bg-secondary d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                    <span class="text-white">No image</span>
                </div>
            @endif
        </div>
        <div class="col-lg-6">
            <h1 class="h3">{{ $product->name }}</h1>
            <p class="fs-4 fw-bold text-primary">৳{{ number_format($product->price, 0) }}</p>
            @if($product->compare_at_price)
                <p class="text-muted text-decoration-line-through">৳{{ number_format($product->compare_at_price, 0) }}</p>
            @endif
            @if($product->sku)
                <p class="small text-muted">SKU: {{ $product->sku }}</p>
            @endif
            @if($product->short_description)
                <p class="mb-3">{{ $product->short_description }}</p>
            @endif
            <form action="{{ route('cart.add') }}" method="POST" class="d-flex align-items-center gap-3">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="input-group" style="width: 120px;">
                    <input type="number" name="quantity" value="1" min="1" max="{{ max(1, $product->stock) }}" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Add to Cart</button>
            </form>
            @if($product->description)
                <div class="mt-4">
                    <h6 class="fw-bold">Description</h6>
                    <div class="small">{!! nl2br(e($product->description)) !!}</div>
                </div>
            @endif
        </div>
    </div>
    @if($related->isNotEmpty())
        <hr class="my-5">
        <h2 class="h5 mb-3">Related Products</h2>
        <div class="row g-3">
            @foreach($related as $p)
                @include('partials.product-card', ['product' => $p])
            @endforeach
        </div>
    @endif
</div>
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
@endsection
