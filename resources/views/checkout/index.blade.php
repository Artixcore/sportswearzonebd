@extends('layouts.app')

@section('title', 'Checkout - ' . config('app.name'))

@section('content')
@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')) });
    }
});
</script>
@endif
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Checkout</h1>

    <form id="checkout-form" method="POST" action="{{ route('checkout.store') }}" class="mt-6 lg:grid lg:grid-cols-3 lg:gap-8">
        @csrf
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="text-lg font-semibold text-gray-900">Customer details</h2>
                <div id="checkout-errors-summary" class="mt-2 hidden rounded-lg bg-red-50 p-3 text-sm text-red-700"></div>
                <div class="mt-6 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', auth()->user()?->name) }}" autocomplete="name" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent">
                        <p class="checkout-error mt-1 text-sm text-red-600 hidden" data-for="name"></p>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone number <span class="text-red-500">*</span></label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="01XXXXXXXXX" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent">
                        <p class="checkout-error mt-1 text-sm text-red-600 hidden" data-for="phone"></p>
                        <p class="mt-1 text-xs text-gray-500">Bangladesh mobile: 11 digits starting with 01</p>
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City <span class="text-red-500">*</span></label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent">
                        <p class="checkout-error mt-1 text-sm text-red-600 hidden" data-for="city"></p>
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address <span class="text-red-500">*</span></label>
                        <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent">{{ old('address') }}</textarea>
                        <p class="checkout-error mt-1 text-sm text-red-600 hidden" data-for="address"></p>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email (optional, for order updates)</label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()?->email) }}" autocomplete="email" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent">
                        <p class="checkout-error mt-1 text-sm text-red-600 hidden" data-for="email"></p>
                    </div>

                    @php $deliveryAdvanceAmount = config('checkout.delivery_advance_amount', 150); @endphp
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h2 class="text-lg font-semibold text-gray-900">Delivery Charge Advance (৳{{ number_format($deliveryAdvanceAmount, 0) }})</h2>
                        <p class="mt-1 text-sm text-gray-600">This is required to confirm your order.</p>
                        <input type="hidden" name="delivery_charge" value="{{ $deliveryAdvanceAmount }}">
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="delivery_advance_method" class="block text-sm font-medium text-gray-700">Payment method <span class="text-red-500">*</span></label>
                                <select id="delivery_advance_method" name="delivery_advance_method" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent">
                                    <option value="">Select method</option>
                                    <option value="bKash" {{ old('delivery_advance_method') === 'bKash' ? 'selected' : '' }}>bKash</option>
                                    <option value="Nagad" {{ old('delivery_advance_method') === 'Nagad' ? 'selected' : '' }}>Nagad</option>
                                    <option value="Rocket" {{ old('delivery_advance_method') === 'Rocket' ? 'selected' : '' }}>Rocket</option>
                                    <option value="Cash" {{ old('delivery_advance_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                </select>
                                <p class="checkout-error mt-1 text-sm text-red-600 hidden" data-for="delivery_advance_method"></p>
                            </div>
                            <div>
                                <label for="delivery_advance_txn_id" class="block text-sm font-medium text-gray-700">Transaction ID</label>
                                <input type="text" id="delivery_advance_txn_id" name="delivery_advance_txn_id" value="{{ old('delivery_advance_txn_id') }}" placeholder="Optional: Transaction ID (if available)" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent">
                                <p class="checkout-error mt-1 text-sm text-red-600 hidden" data-for="delivery_advance_txn_id"></p>
                            </div>
                            <div class="flex items-start">
                                <input type="checkbox" id="delivery_advance_confirmed" name="delivery_advance_confirmed" value="1" {{ old('delivery_advance_confirmed') ? 'checked' : '' }} class="mt-1 h-4 w-4 rounded border-gray-300 text-accent focus:ring-accent">
                                <label for="delivery_advance_confirmed" class="ml-2 block text-sm font-medium text-gray-700">I have paid ৳{{ number_format($deliveryAdvanceAmount, 0) }} delivery charge advance <span class="text-red-500">*</span></label>
                            </div>
                            <p class="checkout-error text-sm text-red-600 hidden" data-for="delivery_advance_confirmed"></p>
                        </div>
                    </div>
                </div>
                <div class="mt-8">
                    <button type="submit" id="checkout-submit-btn" class="w-full rounded-lg bg-accent px-6 py-3 font-medium text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 sm:w-auto sm:min-w-[200px]">Continue to Confirm Order</button>
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
@push('scripts')
<script>
$(function() {
    $('#checkout-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = $('#checkout-submit-btn');
        $('.checkout-error').text('').addClass('hidden');
        $('#checkout-errors-summary').addClass('hidden').text('');
        btn.prop('disabled', true).addClass('opacity-75');
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            dataType: 'json'
        }).done(function(res) {
            if (res.status === 'success' && res.redirect) {
                window.location.href = res.redirect;
                return;
            }
            btn.prop('disabled', false).removeClass('opacity-75');
        }).fail(function(xhr) {
            btn.prop('disabled', false).removeClass('opacity-75');
            var data = xhr.responseJSON;
            if (data && data.errors) {
                $.each(data.errors, function(field, messages) {
                    var el = $('.checkout-error[data-for="' + field + '"]');
                    if (el.length && messages && messages[0]) {
                        el.text(messages[0]).removeClass('hidden');
                    }
                });
            } else if (data && data.message) {
                $('#checkout-errors-summary').text(data.message).removeClass('hidden');
            } else {
                $('#checkout-errors-summary').text('Something went wrong. Please try again.').removeClass('hidden');
            }
            if (data && data.message && typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Validation Error', text: data.message });
            } else if ((!data || !data.message) && typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
            }
        });
    });
});
</script>
@endpush
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
