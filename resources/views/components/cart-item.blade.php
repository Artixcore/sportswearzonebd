@props(['item'])

@php
    $product = $item->product;
    $quantity = (int) $item->quantity;
    $size = $item->size ?? '';
    $lineTotal = $product->final_price * $quantity;
@endphp
<div class="cart-item-row flex flex-col gap-3 border-b border-gray-200 py-4 sm:flex-row sm:items-center sm:gap-4" data-product-id="{{ $product->id }}" data-size="{{ $size }}">
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
        <p class="mt-0.5 text-sm text-gray-500">৳{{ number_format($product->final_price, 0) }} each @if($size)<span class="text-gray-600">· Size: {{ $size }}</span>@endif</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <div class="flex items-center rounded-lg border border-gray-300">
            <button type="button" class="cart-qty-minus flex h-10 w-10 shrink-0 items-center justify-center rounded-l-lg text-gray-600 hover:bg-gray-100" data-product-id="{{ $product->id }}" data-size="{{ $size }}" aria-label="Decrease quantity">−</button>
            <span class="cart-item-qty min-w-[2.5rem] py-2 text-center text-sm font-medium" data-product-id="{{ $product->id }}" data-size="{{ $size }}">{{ $quantity }}</span>
            <button type="button" class="cart-qty-plus flex h-10 w-10 shrink-0 items-center justify-center rounded-r-lg text-gray-600 hover:bg-gray-100" data-product-id="{{ $product->id }}" data-size="{{ $size }}" aria-label="Increase quantity">+</button>
        </div>
        <span class="cart-item-subtotal w-20 text-right font-medium text-gray-900 sm:w-24" data-product-id="{{ $product->id }}" data-price="{{ $product->final_price }}">৳{{ number_format($lineTotal, 0) }}</span>
        <button type="button" class="cart-remove rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" data-product-id="{{ $product->id }}" data-size="{{ $size }}">Remove</button>
    </div>
</div>
