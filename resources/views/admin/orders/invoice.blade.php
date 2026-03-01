<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $order->id }} - {{ config('app.name') }}</title>
    <style>
        body { font-family: system-ui, sans-serif; padding: 24px; color: #1e293b; }
        .no-print { display: none; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px 12px; text-align: left; }
        th { background: #f8fafc; }
        .text-right { text-align: right; }
        .mt-4 { margin-top: 16px; }
        .mb-2 { margin-bottom: 8px; }
    </style>
</head>
<body>
    <a href="{{ route('admin.orders.show', $order) }}" class="no-print" style="position:absolute;top:16px;right:16px;">← Back to order</a>
    <h1>Invoice #{{ $order->id }}</h1>
    <p class="mb-2"><strong>{{ config('app.name') }}</strong></p>
    <p class="mb-2">Date: {{ $order->created_at->format('M d, Y H:i') }}</p>

    <div class="mt-4">
        <strong>Bill To / Ship To</strong>
        <p>{{ $order->shipping_name }}</p>
        <p>{{ $order->shipping_phone }}</p>
        <p>{{ $order->shipping_city }}</p>
        <p>{{ $order->shipping_address }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-right">Price</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td class="text-right">৳{{ number_format($item->price, 0) }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">৳{{ number_format($item->price * $item->quantity, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="mt-4"><strong>Subtotal:</strong> ৳{{ number_format($order->subtotal, 0) }}</p>
    <p><strong>Shipping:</strong> ৳{{ number_format($order->shipping, 0) }}</p>
    <p><strong>Total:</strong> ৳{{ number_format($order->total, 0) }}</p>
    <p><strong>Payment:</strong> {{ ucfirst($order->payment_method ?? 'COD') }}</p>
    <p class="mt-4 text-sm text-slate-500">Status: {{ $order->status }}</p>

    <script>
    window.onload = function() {
        if (window.location.search.indexOf('print=1') !== -1) window.print();
    };
    </script>
</body>
</html>
