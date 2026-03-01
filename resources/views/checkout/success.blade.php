@extends('layouts.store')

@section('title', 'Order Confirmed - ' . config('app.name'))

@section('content')
<div class="container py-5 text-center">
    <h1 class="h3 mb-3">Thank you for your order!</h1>
    <p class="text-muted mb-4">Order #{{ $order->id }}</p>
    <div class="card text-start mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <p><strong>Total:</strong> ৳{{ number_format($order->total, 0) }}</p>
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
            <p class="mb-0 small text-muted">We will contact you at {{ $order->guest_email }} for updates.</p>
        </div>
    </div>
    <a href="{{ route('shop.index') }}" class="btn btn-primary mt-4">Continue Shopping</a>
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
