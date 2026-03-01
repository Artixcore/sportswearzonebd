@extends('layouts.admin')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-semibold text-slate-800">Order #{{ $order->id }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.orders.invoice', $order) }}" class="inline-flex px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300" target="_blank">Print Invoice</a>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 font-medium">Items</div>
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Product</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Price</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Qty</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($order->items as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item->name }}</td>
                        <td class="px-4 py-2 text-right">৳{{ number_format($item->price, 0) }}</td>
                        <td class="px-4 py-2 text-right">{{ $item->quantity }}</td>
                        <td class="px-4 py-2 text-right">৳{{ number_format($item->price * $item->quantity, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
            <h2 class="font-medium text-slate-800 mb-3">Summary</h2>
            <p class="flex justify-between text-sm"><span class="text-slate-600">Subtotal</span>৳{{ number_format($order->subtotal, 0) }}</p>
            <p class="flex justify-between text-sm"><span class="text-slate-600">Shipping</span>৳{{ number_format($order->shipping, 0) }}</p>
            <p class="flex justify-between text-sm font-medium mt-2"><span>Total</span>৳{{ number_format($order->total, 0) }}</p>
            <p class="flex justify-between text-sm mt-1"><span class="text-slate-600">Payment</span>{{ ucfirst($order->payment_method ?? 'cod') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
            <h2 class="font-medium text-slate-800 mb-3">Shipping</h2>
            <p class="text-sm">{{ $order->shipping_name }}</p>
            <p class="text-sm">{{ $order->shipping_phone }}</p>
            <p class="text-sm">{{ $order->shipping_city }}</p>
            <p class="text-sm text-slate-600">{{ $order->shipping_address }}</p>
        </div>
        @if($order->delivery_advance_customer_confirmed || (isset($order->delivery_advance_paid) && (float) $order->delivery_advance_paid > 0))
        <div class="bg-white rounded-lg shadow border border-slate-200 p-4" id="delivery-advance-block">
            <h2 class="font-medium text-slate-800 mb-3">Delivery Advance Verification</h2>
            <dl class="text-sm space-y-1 mb-4">
                <p class="flex justify-between"><span class="text-slate-600">Amount</span><span>৳{{ number_format($order->delivery_advance_paid ?? config('checkout.delivery_advance_amount', 150), 0) }}</span></p>
                <p class="flex justify-between"><span class="text-slate-600">Method</span><span>{{ $order->delivery_advance_method ?? '—' }}</span></p>
                <p class="flex justify-between"><span class="text-slate-600">Customer Txn ID</span><span>{{ !empty($order->delivery_advance_txn_id) ? $order->delivery_advance_txn_id : 'Not provided' }}</span></p>
                <p class="flex justify-between"><span class="text-slate-600">Customer confirmed</span><span>{{ $order->delivery_advance_customer_confirmed ? 'Yes' : 'No' }}</span></p>
            </dl>
            <form id="delivery-advance-form" action="{{ route('admin.orders.updateDeliveryAdvance', $order) }}" method="POST" class="space-y-2">
                @csrf
                <div>
                    <label for="delivery_advance_admin_txn_id" class="block text-xs font-medium text-slate-600 mb-1">Admin Transaction ID <span class="text-red-500">*</span></label>
                    <input type="text" id="delivery_advance_admin_txn_id" name="delivery_advance_admin_txn_id" value="{{ old('delivery_advance_admin_txn_id', $order->delivery_advance_admin_txn_id) }}" class="w-full rounded border-slate-300 shadow-sm text-sm" required>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="delivery_advance_admin_verified" value="0">
                    <input type="checkbox" id="delivery_advance_admin_verified" name="delivery_advance_admin_verified" value="1" {{ $order->delivery_advance_admin_verified ? 'checked' : '' }} class="rounded border-slate-300">
                    <label for="delivery_advance_admin_verified" class="text-sm text-slate-700">Verified</label>
                </div>
                <button type="submit" id="delivery-advance-submit" class="w-full px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-700 text-sm">Save verification</button>
            </form>
        </div>
        @endif
        <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
            <h2 class="font-medium text-slate-800 mb-3">Update Status</h2>
            <form id="order-status-form" action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                @csrf
                <select name="status" id="order-status-select" class="w-full rounded border-slate-300 shadow-sm mb-2">
                    @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" id="order-status-submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Update</button>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken && typeof $ !== 'undefined') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json'
            }
        });
    }

    function showSwal(type, title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: type, title: title || '', text: text || '' });
        }
    }

    var deliveryAdvanceForm = document.getElementById('delivery-advance-form');
    if (deliveryAdvanceForm && typeof $ !== 'undefined') {
        $(deliveryAdvanceForm).on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = document.getElementById('delivery-advance-submit');
            if (btn) btn.disabled = true;
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                dataType: 'json'
            }).done(function(res) {
                if (res.status === 'success') showSwal('success', 'Success', res.message);
                if (btn) btn.disabled = false;
            }).fail(function(xhr) {
                if (btn) btn.disabled = false;
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Request failed.';
                showSwal('error', 'Error', msg);
            });
        });
    }

    var statusForm = document.getElementById('order-status-form');
    if (statusForm && typeof $ !== 'undefined') {
        $(statusForm).on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = document.getElementById('order-status-submit');
            if (btn) btn.disabled = true;
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                dataType: 'json'
            }).done(function(res) {
                if (res.status === 'success') showSwal('success', 'Success', res.message);
                if (btn) btn.disabled = false;
            }).fail(function(xhr) {
                if (btn) btn.disabled = false;
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Could not update status.';
                showSwal('error', 'Error', msg);
            });
        });
    }
});
</script>
@endpush
@endsection
