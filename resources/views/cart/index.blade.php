@extends('layouts.app')

@section('title', 'Cart - ' . config('app.name'))

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Shopping Cart</h1>

    @if(empty($cartItems))
        <div class="mt-8 rounded-xl border border-gray-200 bg-white p-8 text-center">
            <p class="text-gray-600">Your cart is empty.</p>
            <a href="{{ route('shop.index') }}" class="mt-4 inline-block rounded-lg bg-accent px-6 py-2.5 font-medium text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">Continue Shopping</a>
        </div>
    @else
        <div class="mt-6 lg:grid lg:grid-cols-3 lg:gap-8">
            <div class="lg:col-span-2">
                <div class="rounded-xl border border-gray-200 bg-white px-4 shadow-sm sm:px-6">
                    @foreach($cartItems as $item)
                        <x-cart-item :item="$item" />
                    @endforeach
                </div>
                <div class="mt-4 flex justify-end">
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Clear all items from cart?');">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 focus:outline-none">Clear cart</button>
                    </form>
                </div>
            </div>

            <div class="mt-6 lg:mt-0">
                <div class="sticky top-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    @php
                        $subtotal = 0;
                        foreach ($cartItems as $item) {
                            $subtotal += $item->product->price * $item->quantity;
                        }
                        $delivery = 0;
                        $total = $subtotal + $delivery;
                    @endphp
                    <h2 class="text-lg font-semibold text-gray-900">Summary</h2>
                    <dl class="mt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-600">Subtotal</dt>
                            <dd class="font-medium text-gray-900">৳{{ number_format($subtotal, 0) }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-600">Delivery</dt>
                            <dd class="font-medium text-gray-900">৳{{ number_format($delivery, 0) }}</dd>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-3 text-base">
                            <dt class="font-semibold text-gray-900">Total</dt>
                            <dd class="font-semibold text-gray-900">৳{{ number_format($total, 0) }}</dd>
                        </div>
                    </dl>
                    <a href="{{ route('checkout.index') }}" class="mt-6 block w-full rounded-lg bg-accent py-3 text-center font-medium text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">Proceed to Checkout</a>
                    <a href="{{ route('shop.index') }}" class="mt-3 block w-full rounded-lg border-2 border-gray-300 py-3 text-center font-medium text-gray-700 hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">Continue Shopping</a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
