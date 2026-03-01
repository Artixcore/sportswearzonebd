@extends('layouts.app')

@section('title', 'Cart - ' . config('app.name'))

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Shopping Cart</h1>

    <div id="cart-empty-state" class="mt-8 rounded-xl border border-gray-200 bg-white p-8 text-center" style="{{ empty($cartItems) ? '' : 'display:none' }}">
        <p class="text-gray-600">Your cart is empty.</p>
        <a href="{{ route('shop.index') }}" class="mt-4 inline-block rounded-lg bg-accent px-6 py-2.5 font-medium text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">Continue Shopping</a>
    </div>

    <div id="cart-has-items" style="{{ empty($cartItems) ? 'display:none' : '' }}">
        <div class="mt-6 lg:grid lg:grid-cols-3 lg:gap-8">
            <div class="lg:col-span-2">
                <div id="cart-items-container" class="rounded-xl border border-gray-200 bg-white px-4 shadow-sm sm:px-6">
                    @foreach($cartItems as $item)
                        <x-cart-item :item="$item" />
                    @endforeach
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="button" id="cart-clear-btn" class="text-sm font-medium text-red-600 hover:text-red-700 focus:outline-none">Clear cart</button>
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
                            <dd id="cart-subtotal" class="font-medium text-gray-900">৳{{ number_format($subtotal, 0) }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-600">Delivery</dt>
                            <dd class="font-medium text-gray-900">৳{{ number_format($delivery, 0) }}</dd>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-3 text-base">
                            <dt class="font-semibold text-gray-900">Total</dt>
                            <dd id="cart-total" class="font-semibold text-gray-900">৳{{ number_format($total, 0) }}</dd>
                        </div>
                    </dl>
                    <a href="{{ route('checkout.index') }}" class="mt-6 block w-full rounded-lg bg-accent py-3 text-center font-medium text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">Proceed to Checkout</a>
                    <a href="{{ route('shop.index') }}" class="mt-3 block w-full rounded-lg border-2 border-gray-300 py-3 text-center font-medium text-gray-700 hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var updateUrl = @json(route('cart.update'));
    var removeUrlBase = @json(url('/cart/remove'));
    var clearUrl = @json(route('cart.clear'));
    function removeUrlFor(id) { return removeUrlBase + '/' + id; }
    function formatMoney(n) { return '৳' + Math.round(n).toLocaleString(); }
    function updateSummary(cartTotal) {
        $('#cart-subtotal, #cart-total').text(formatMoney(cartTotal));
    }
    $(function() {
        $(document).on('click', '.cart-qty-minus', function() {
            var btn = $(this);
            var pid = btn.data('product-id');
            var row = btn.closest('.cart-item-row');
            var qtyEl = row.find('.cart-item-qty');
            var qty = parseInt(qtyEl.text(), 10) || 1;
            var newQty = Math.max(0, qty - 1);
            if (newQty === qty) return;
            btn.prop('disabled', true);
            $.post(updateUrl, { product_id: pid, quantity: newQty, _token: $('meta[name="csrf-token"]').attr('content') })
                .done(function(res) {
                    if (typeof updateNavCartCount === 'function') updateNavCartCount(res.cart_count);
                    if (res.removed) {
                        row.remove();
                        if ($('.cart-item-row').length === 0) {
                            $('#cart-has-items').hide();
                            $('#cart-empty-state').show();
                        }
                    } else {
                        qtyEl.text(res.item_subtotal ? (newQty) : newQty);
                        var subtotalEl = row.find('.cart-item-subtotal');
                        if (subtotalEl.length) subtotalEl.text(formatMoney(res.item_subtotal));
                    }
                    updateSummary(res.cart_total);
                })
                .fail(function() {
                    if (typeof showToast === 'function') showToast('Could not update quantity.', 'error');
                })
                .always(function() { btn.prop('disabled', false); });
        });
        $(document).on('click', '.cart-qty-plus', function() {
            var btn = $(this);
            var pid = btn.data('product-id');
            var row = btn.closest('.cart-item-row');
            var qtyEl = row.find('.cart-item-qty');
            var qty = parseInt(qtyEl.text(), 10) || 0;
            var newQty = qty + 1;
            btn.prop('disabled', true);
            $.post(updateUrl, { product_id: pid, quantity: newQty, _token: $('meta[name="csrf-token"]').attr('content') })
                .done(function(res) {
                    if (typeof updateNavCartCount === 'function') updateNavCartCount(res.cart_count);
                    qtyEl.text(newQty);
                    row.find('.cart-item-subtotal').text(formatMoney(res.item_subtotal));
                    updateSummary(res.cart_total);
                })
                .fail(function() {
                    if (typeof showToast === 'function') showToast('Could not update quantity.', 'error');
                })
                .always(function() { btn.prop('disabled', false); });
        });
        $(document).on('click', '.cart-remove', function() {
            var btn = $(this);
            var pid = btn.data('product-id');
            var row = btn.closest('.cart-item-row');
            btn.prop('disabled', true);
            $.post(removeUrlFor(pid), { _token: $('meta[name="csrf-token"]').attr('content') })
                .done(function(res) {
                    if (typeof updateNavCartCount === 'function') updateNavCartCount(res.cart_count);
                    row.remove();
                    if ($('.cart-item-row').length === 0) {
                        $('#cart-has-items').hide();
                        $('#cart-empty-state').show();
                    }
                    updateSummary(res.cart_total);
                    if (typeof showToast === 'function') showToast('Item removed.', 'success');
                })
                .fail(function() {
                    if (typeof showToast === 'function') showToast('Could not remove item.', 'error');
                    btn.prop('disabled', false);
                });
        });
        $('#cart-clear-btn').on('click', function() {
            if (!confirm('Clear all items from cart?')) return;
            var btn = $(this);
            btn.prop('disabled', true);
            $.post(clearUrl, { _token: $('meta[name="csrf-token"]').attr('content') })
                .done(function(res) {
                    if (typeof updateNavCartCount === 'function') updateNavCartCount(res.cart_count);
                    $('#cart-items-container').empty();
                    $('#cart-has-items').hide();
                    $('#cart-empty-state').show();
                    if (typeof showToast === 'function') showToast('Cart cleared.', 'success');
                })
                .fail(function() {
                    if (typeof showToast === 'function') showToast('Could not clear cart.', 'error');
                })
                .always(function() { btn.prop('disabled', false); });
        });
    });
})();
</script>
@endpush
@endsection
