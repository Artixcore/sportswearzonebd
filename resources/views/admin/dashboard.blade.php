@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 class="h4 mb-4">Dashboard</h1>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Revenue</h6>
                <h4 class="mb-0">৳{{ number_format($revenue, 0) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Orders</h6>
                <h4 class="mb-0">{{ $ordersCount }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Products</h6>
                <h4 class="mb-0">{{ $productsCount }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Customers</h6>
                <h4 class="mb-0">{{ $customersCount }}</h4>
            </div>
        </div>
    </div>
</div>
<h6 class="fw-bold mb-2">Recent Orders</h6>
<div class="table-responsive">
    <table class="table table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentOrders as $order)
                <tr>
                    <td><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->id }}</a></td>
                    <td>{{ $order->user?->name ?? $order->guest_email }}</td>
                    <td>৳{{ number_format($order->total, 0) }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
