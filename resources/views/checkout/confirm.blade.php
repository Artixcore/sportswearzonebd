@extends('layouts.app')

@section('title', 'Confirm Order - ' . config('app.name'))

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Confirm your order</h1>

    <div class="mt-6 lg:grid lg:grid-cols-3 lg:gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Delivery details</h2>
                    <a href="{{ route('checkout.index') }}" class="text-sm font-medium text-accent hover:text-accent-hover focus:outline-none focus:underline">Edit</a>
                </div>
                <dl class="mt-4 space-y-2 text-sm">
                    <div>
                        <dt class="text-gray-500">Name</dt>
                        <dd class="font-medium text-gray-900">{{ $customer['name'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="font-medium text-gray-900">{{ $customer['phone'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">City</dt>
                        <dd class="font-medium text-gray-900">{{ $customer['city'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Address</dt>
                        <dd class="font-medium text-gray-900">{{ $customer['address'] }}</dd>
                    </div>
                    @if(!empty($customer['email']))
                        <div>
                            <dt class="text-gray-500">Email</dt>
                            <dd class="font-medium text-gray-900">{{ $customer['email'] }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Items</h2>
                <ul class="mt-4 divide-y divide-gray-200">
                    @foreach($cartItems as $item)
                        <li class="flex justify-between py-3">
                            <div class="flex gap-3">
                                @if($item->product->primaryImage)
                                    <img src="{{ asset('storage/' . $item->product->primaryImage->path) }}" alt="" class="h-14 w-14 shrink-0 rounded-lg object-cover">
                                @else
                                    <div class="h-14 w-14 shrink-0 rounded-lg bg-muted"></div>
                                @endif
                                <div>
                                    <span class="font-medium text-gray-900">{{ $item->product->name }}</span>
                                    <span class="block text-sm text-gray-500">Qty: {{ $item->quantity }} @if(!empty($item->size))· Size: {{ $item->size }}@endif</span>
                                </div>
                            </div>
                            <span class="font-medium text-gray-900">৳{{ number_format($item->product->final_price * $item->quantity, 0) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-6 lg:mt-0">
            <div class="sticky top-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Summary</h2>
                <dl class="mt-4 space-y-2">
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
                <p class="mt-4 rounded-lg bg-muted px-3 py-2 text-sm text-gray-700">
                    <strong>Payment:</strong> Cash on Delivery
                </p>
                <form id="confirm-order-form" action="{{ route('orders.store') }}" method="POST" class="mt-6">
                    @csrf
                    <button type="submit" id="confirm-order-btn" class="w-full rounded-lg bg-accent py-3 font-medium text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">Confirm Order</button>
                </form>
                <a href="{{ route('cart.index') }}" class="mt-3 block w-full rounded-lg border-2 border-gray-300 py-3 text-center font-medium text-gray-700 hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">Back to Cart</a>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
$(function() {
    $('#confirm-order-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = $('#confirm-order-btn');
        btn.prop('disabled', true).addClass('opacity-75');
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            dataType: 'json'
        }).done(function(res) {
            if (res.status === 'success' && res.redirect) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Success', text: res.message || 'Order placed successfully.' }).then(function() {
                        window.location.href = res.redirect;
                    });
                } else {
                    window.location.href = res.redirect;
                }
                return;
            }
            btn.prop('disabled', false).removeClass('opacity-75');
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.' });
            else if (typeof showToast === 'function') showToast('Something went wrong.', 'error');
        }).fail(function(xhr) {
            btn.prop('disabled', false).removeClass('opacity-75');
            var msg = 'Could not place order.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: msg });
            else if (typeof showAlert === 'function') showAlert('error', 'Error', msg);
            else if (typeof showToast === 'function') showToast(msg, 'error');
        });
    });
});
</script>
@endpush
@endsection
