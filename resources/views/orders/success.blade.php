@extends('layouts.app')

@section('title', 'Order Confirmed - ' . config('app.name'))

@section('content')
<div class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-green-600">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="mt-4 text-2xl font-bold text-gray-900">Thank you for your order!</h1>
        <p class="mt-2 text-gray-600">Order #{{ $order->id }}</p>
        <dl class="mt-6 space-y-2 text-left sm:mx-auto sm:max-w-xs">
            <div class="flex justify-between text-sm">
                <dt class="text-gray-500">Total</dt>
                <dd class="font-semibold text-gray-900">৳{{ number_format($order->total, 0) }}</dd>
            </div>
            <div class="flex justify-between text-sm">
                <dt class="text-gray-500">Status</dt>
                <dd class="font-medium text-gray-900">{{ ucfirst($order->status) }}</dd>
            </div>
        </dl>
        @if($order->guest_email)
            <p class="mt-4 text-sm text-gray-500">We will contact you at {{ $order->guest_email }} for updates.</p>
        @elseif($order->shipping_phone)
            <p class="mt-4 text-sm text-gray-500">We will contact you at {{ $order->shipping_phone }} for updates.</p>
        @endif
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('shop.index') }}" class="inline-block rounded-lg bg-accent px-6 py-2.5 font-medium text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">Continue Shopping</a>
            <a href="{{ route('home') }}" class="inline-block rounded-lg border-2 border-gray-300 px-6 py-2.5 font-medium text-gray-700 hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">Back to Home</a>
        </div>
    </div>
</div>
@php $purchaseEventId = $order->meta['event_id'] ?? null; $metaPixelId = \App\Models\Setting::get('meta_pixel_id', config('meta.pixel_id')); @endphp
@if($purchaseEventId && $metaPixelId)
@push('scripts')
<script>
    if (typeof fbq !== 'undefined') {
        fbq('track', 'Purchase', { value: {{ $order->total }}, currency: 'BDT', content_ids: @json($order->items->pluck('product_id')->filter()->values()->toArray()), num_items: {{ $order->items->sum('quantity') }} }, { eventID: '{{ $purchaseEventId }}' });
    }
</script>
@endpush
@endif
@endsection
