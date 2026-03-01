<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt #{{ $sale->id }} - {{ config('app.name') }}</title>
    <style>
        body { font-family: system-ui, sans-serif; padding: 24px; color: #1e293b; max-width: 400px; margin: 0 auto; }
        .no-print { display: none; }
        @media print {
            body { padding: 16px; }
            .no-print { display: none !important; }
        }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 6px 8px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .text-right { text-align: right; }
        .mt-4 { margin-top: 16px; }
        .mb-2 { margin-bottom: 4px; }
        h1 { font-size: 1.25rem; margin-bottom: 8px; }
    </style>
</head>
<body>
    <a href="{{ route('admin.pos.index') }}" class="no-print" style="position:absolute;top:16px;right:16px;">← New sale</a>
    <h1>{{ config('app.name') }}</h1>
    <p class="mb-2">Receipt #{{ $sale->id }}</p>
    <p class="mb-2">{{ $sale->created_at->format('M d, Y H:i') }}</p>
    @if($sale->customer)
        <p class="mb-2">Customer: {{ $sale->customer->name }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Price</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td class="text-right">৳{{ number_format($item->price, 0) }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">৳{{ number_format($item->price * $item->quantity - $item->discount, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="mt-4"><strong>Subtotal:</strong> ৳{{ number_format($sale->subtotal, 0) }}</p>
    @if($sale->discount > 0)
        <p><strong>Discount:</strong> ৳{{ number_format($sale->discount, 0) }}</p>
    @endif
    <p><strong>Total:</strong> ৳{{ number_format($sale->total, 0) }}</p>
    <p><strong>Payment:</strong> {{ ucfirst($sale->payment_method) }}</p>
    <p class="mt-4 text-sm text-slate-500">Thank you!</p>

    <script>if (window.location.search.indexOf('print=1') !== -1) window.print();</script>
</body>
</html>
