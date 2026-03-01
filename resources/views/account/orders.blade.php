@extends('layouts.store')

@section('title', 'My Orders - ' . config('app.name'))

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-4">My Orders</h1>
    @if($orders->isEmpty())
        <p class="text-muted">You have no orders yet.</p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary">Shop Now</a>
    @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>৳{{ number_format($order->total, 0) }}</td>
                            <td>{{ ucfirst($order->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $orders->links() }}
    @endif
</div>
@endsection
