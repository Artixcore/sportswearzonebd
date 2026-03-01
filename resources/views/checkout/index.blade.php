@extends('layouts.app')

@section('title', 'Checkout - ' . config('app.name'))

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Checkout</h1>

    <form method="POST" action="{{ route('checkout.store') }}" class="mt-6 lg:grid lg:grid-cols-3 lg:gap-8">
        @csrf
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="text-lg font-semibold text-gray-900">Customer details</h2>
                <div class="mt-6 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', auth()->user()?->name) }}" required autocomplete="name" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent @error('name') border-red-500 @enderror">
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone number <span class="text-red-500">*</span></label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="01XXXXXXXXX" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent @error('phone') border-red-500 @enderror">
                        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500">Bangladesh mobile: 11 digits starting with 01</p>
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City <span class="text-red-500">*</span></label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent @error('city') border-red-500 @enderror">
                        @error('city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address <span class="text-red-500">*</span></label>
                        <textarea id="address" name="address" rows="3" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                        @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email (optional, for order updates)</label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()?->email) }}" autocomplete="email" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent @error('email') border-red-500 @enderror">
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-8">
                    <button type="submit" class="w-full rounded-lg bg-accent px-6 py-3 font-medium text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 sm:w-auto sm:min-w-[200px]">Continue to Confirm Order</button>
                </div>
            </div>
        </div>

        <div class="mt-6 lg:mt-0">
            <div class="sticky top-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Order summary</h2>
                <ul class="mt-4 divide-y divide-gray-200">
                    @foreach($cartItems as $item)
                        <li class="flex justify-between py-3 text-sm">
                            <span class="text-gray-700">{{ $item->product->name }} × {{ $item->quantity }}</span>
                            <span class="font-medium text-gray-900">৳{{ number_format($item->product->price * $item->quantity, 0) }}</span>
                        </li>
                    @endforeach
                </ul>
                <dl class="mt-4 space-y-2 border-t border-gray-200 pt-4">
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-600">Subtotal</dt>
                        <dd class="font-medium text-gray-900">৳{{ number_format($subtotal, 0) }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-600">Delivery</dt>
                        <dd class="font-medium text-gray-900">৳{{ number_format($shipping, 0) }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-3 text-base font-semibold">
                        <dt class="text-gray-900">Total</dt>
                        <dd class="text-gray-900">৳{{ number_format($total, 0) }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </form>
</div>
@if(isset($initiateEventId) && ($metaPixelId = \App\Models\Setting::get('meta_pixel_id', config('meta.pixel_id'))))
@push('scripts')
<script>
    if (typeof fbq !== 'undefined') {
        fbq('track', 'InitiateCheckout', { value: {{ $total }}, currency: 'BDT', content_ids: @json(collect($cartItems)->map(fn($i) => $i->product->id)->values()->toArray()), num_items: {{ collect($cartItems)->sum('quantity') }} }, { eventID: '{{ $initiateEventId }}' });
    }
</script>
@endpush
@endif
@endsection
