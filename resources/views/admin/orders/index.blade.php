@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<h1 class="h4 mb-4">Orders</h1>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->user?->name ?? $order->guest_email }}</td>
                    <td>৳{{ number_format($order->total, 0) }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                    <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $orders->links() }}
@endsection
