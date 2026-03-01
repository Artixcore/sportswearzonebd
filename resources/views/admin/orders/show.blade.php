@extends('layouts.admin')

@section('title', 'Order #' . $order->id)

@section('content')
<h1 class="h4 mb-4">Order #{{ $order->id }}</h1>
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">Items</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>৳{{ number_format($item->price, 0) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>৳{{ number_format($item->price * $item->quantity, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">Summary</div>
            <div class="card-body">
                <p class="d-flex justify-content-between"><span>Subtotal</span>৳{{ number_format($order->subtotal, 0) }}</p>
                <p class="d-flex justify-content-between"><span>Shipping</span>৳{{ number_format($order->shipping, 0) }}</p>
                <p class="d-flex justify-content-between fw-bold"><span>Total</span>৳{{ number_format($order->total, 0) }}</p>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">Shipping</div>
            <div class="card-body small">
                <p class="mb-0">{{ $order->shipping_name }}</p>
                <p class="mb-0">{{ $order->shipping_phone }}</p>
                <p class="mb-0">{{ $order->shipping_address }}</p>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Update Status</div>
            <div class="card-body">
                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                    @csrf
                    <select name="status" class="form-select mb-2">
                        @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
