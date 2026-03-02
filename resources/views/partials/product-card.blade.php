<div class="col-6 col-md-4 col-lg-3">
    <div class="card h-100 border-0 shadow-sm">
        <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none text-dark">
            @if($product->primaryImage)
                <img src="{{ storage_asset($product->primaryImage->path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
            @else
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                    <span class="text-white">No image</span>
                </div>
            @endif
            <div class="card-body">
                <h6 class="card-title small">{{ Str::limit($product->name, 40) }}</h6>
                <p class="mb-0">
                    <span class="fw-bold">৳{{ number_format($product->price, 0) }}</span>
                    @if($product->discount_percent)
                        <span class="text-danger small ms-1">{{ $product->discount_percent }}% Off</span>
                    @endif
                </p>
            </div>
        </a>
        <div class="card-footer bg-white border-0 pt-0">
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-primary btn-sm w-100">Add to Cart</button>
            </form>
        </div>
    </div>
</div>
