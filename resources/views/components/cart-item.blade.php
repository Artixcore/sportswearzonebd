@props(['item'])

@php
    $product = $item->product;
    $quantity = (int) $item->quantity;
    $lineTotal = $product->price * $quantity;
@endphp
<div class="flex flex-col gap-3 border-b border-gray-200 py-4 sm:flex-row sm:items-center sm:gap-4">
    <a href="{{ route('product.show', $product->slug) }}" class="shrink-0 overflow-hidden rounded-lg bg-muted sm:w-24 sm:aspect-square">
        @if($product->primaryImage)
            <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}" class="h-24 w-full object-cover sm:h-full">
        @else
            <div class="flex h-24 w-full items-center justify-center text-gray-400 sm:h-full">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>
            </div>
        @endif
    </a>
    <div class="min-w-0 flex-1">
        <a href="{{ route('product.show', $product->slug) }}" class="font-medium text-gray-900 hover:text-accent">{{ $product->name }}</a>
        <p class="mt-0.5 text-sm text-gray-500">৳{{ number_format($product->price, 0) }} each</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <form action="{{ route('cart.update') }}" method="POST" class="flex items-center rounded-lg border border-gray-300">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <button type="submit" name="quantity" value="{{ max(1, $quantity - 1) }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-l-lg text-gray-600 hover:bg-gray-100" aria-label="Decrease quantity">−</button>
            <span class="min-w-[2.5rem] py-2 text-center text-sm font-medium">{{ $quantity }}</span>
            <button type="submit" name="quantity" value="{{ $quantity + 1 }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-r-lg text-gray-600 hover:bg-gray-100" aria-label="Increase quantity">+</button>
        </form>
        <span class="w-20 text-right font-medium text-gray-900 sm:w-24">৳{{ number_format($lineTotal, 0) }}</span>
        <form action="{{ route('cart.remove', $product->id) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Remove</button>
        </form>
    </div>
</div>
