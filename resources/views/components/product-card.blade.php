@props(['product'])

<article class="flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-surface shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
    <a href="{{ route('product.show', $product->slug) }}" class="group block flex-shrink-0 overflow-hidden">
        <div class="relative aspect-square bg-muted">
            @if($product->primaryImage)
                <img src="{{ asset('storage/' . $product->primaryImage->path) }}"
                     alt="{{ $product->name }}"
                     class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105">
            @else
                <div class="flex h-full w-full items-center justify-center text-gray-400">
                    <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>
                </div>
            @endif
            <span class="absolute left-2 top-2 rounded-md bg-base px-2 py-0.5 text-xs font-medium text-white">
                @if($product->category)
                    {{ $product->category->parent_id && $product->category->parent ? $product->category->parent->name . ' › ' . $product->category->name : $product->category->name }}
                @else
                    Uncategorized
                @endif
            </span>
        </div>
    </a>
    <div class="flex flex-1 flex-col p-3">
        <a href="{{ route('product.show', $product->slug) }}" class="mt-1 block flex-1">
            <h3 class="text-sm font-medium text-gray-900 line-clamp-2 hover:text-accent">{{ Str::limit($product->name, 40) }}</h3>
        </a>
        {{-- Mock rating --}}
        <div class="mt-1 flex items-center gap-0.5 text-amber-500" aria-hidden="true">
            @for($i = 1; $i <= 5; $i++)
                <svg class="h-4 w-4 {{ $i <= 4 ? 'fill-current' : 'fill-gray-200' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            @endfor
            <span class="ml-1 text-xs text-gray-500">4.0</span>
        </div>
        <div class="mt-2 flex items-center gap-2">
            @if($product->has_discount)
                <span class="text-sm text-gray-500 line-through">৳{{ number_format($product->original_price, 0) }}</span>
                <span class="text-base font-bold text-gray-900">৳{{ number_format($product->final_price, 0) }}</span>
                @if($product->discount_label)
                    <span class="rounded bg-red-100 px-1.5 py-0.5 text-xs font-medium text-red-700">{{ $product->discount_label }}</span>
                @endif
            @else
                <span class="text-base font-bold text-gray-900">৳{{ number_format($product->final_price, 0) }}</span>
            @endif
        </div>
        <form action="{{ route('cart.add') }}" method="POST" class="mt-3">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="w-full rounded-lg bg-accent px-3 py-2 text-sm font-medium text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                Add to cart
            </button>
        </form>
    </div>
</article>
