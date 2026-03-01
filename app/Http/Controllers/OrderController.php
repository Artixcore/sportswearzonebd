<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $cart = session('cart', []);
        $customer = session('checkout_customer');

        if (empty($cart)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Your cart is empty.'], 422);
            }
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        if (empty($customer)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Please enter your details first.'], 422);
            }
            return redirect()->route('checkout.index')->with('error', 'Please enter your details first.');
        }

        $productIds = [];
        foreach (array_keys($cart) as $k) {
            $parts = explode('_', (string) $k, 2);
            $productIds[(int) $parts[0]] = true;
        }
        $productIds = array_keys($productIds);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $subtotal = 0;
        $items = [];
        $insufficient = [];
        foreach ($cart as $key => $qty) {
            $parts = explode('_', (string) $key, 2);
            $id = (int) $parts[0];
            $size = $parts[1] ?? '';
            if (! $products->has($id)) {
                continue;
            }
            $p = $products[$id];
            $qty = (int) $qty;
            $available = (int) ($p->stock ?? 0);
            if ($available < $qty) {
                $insufficient[] = $p->name . ($available > 0 ? ' (available: ' . $available . ')' : ' (out of stock)');
            }
            $subtotal += $p->final_price * $qty;
            $items[] = [
                'product_id' => $p->id,
                'name' => $p->name,
                'price' => $p->final_price,
                'quantity' => $qty,
                'sku' => $p->sku,
                'size' => $size,
            ];
        }

        if (! empty($insufficient)) {
            $message = 'Insufficient stock: ' . implode(', ', $insufficient);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $message], 422);
            }
            return redirect()->back()->with('error', $message);
        }

        $shipping = 0;
        $tax = 0;
        $total = $subtotal + $shipping + $tax;
        $purchaseEventId = Str::uuid()->toString();

        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'guest_email' => $customer['email'] ?? null,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'currency' => 'BDT',
                'payment_method' => 'cod',
                'source' => 'online',
                'meta' => [
                    'utm_source' => $request->get('utm_source'),
                    'utm_medium' => $request->get('utm_medium'),
                    'utm_campaign' => $request->get('utm_campaign'),
                    'event_id' => $purchaseEventId,
                ],
                'shipping_name' => $customer['name'],
                'shipping_phone' => $customer['phone'],
                'shipping_city' => $customer['city'],
                'shipping_address' => $customer['address'],
                'billing_name' => $customer['name'],
                'billing_phone' => $customer['phone'],
                'billing_address' => $customer['address'],
                'delivery_charge' => 0,
                'delivery_advance_paid' => null,
                'delivery_advance_method' => null,
                'delivery_advance_txn_id' => null,
                'delivery_advance_customer_confirmed' => false,
                'delivery_advance_admin_txn_id' => null,
                'delivery_advance_admin_verified' => false,
                'delivery_settlement_status' => 'pending',
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'sku' => $item['sku'],
                    'size' => $item['size'] ?? null,
                ]);
            }

            session()->forget('cart');
            session()->forget('checkout_customer');
            session()->flash('last_order_id', $order->id);

            $capi = app(\App\Services\MetaConversionsApiService::class);
            if ($capi->isConfigured()) {
                try {
                    $capi->sendPurchase($order, $purchaseEventId);
                } catch (Throwable $e) {
                    Log::warning('Meta Conversions API sendPurchase failed', ['order_id' => $order->id, 'message' => $e->getMessage()]);
                }
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Order placed successfully.',
                    'redirect' => route('orders.success'),
                ]);
            }

            return redirect()->route('orders.success')->with('success', 'Order placed successfully.');
        } catch (Throwable $e) {
            Log::error('Order creation failed', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Unable to place order. Please try again.'], 500);
            }

            return redirect()->back()->with('error', 'Unable to place order. Please try again.');
        }
    }

    public function success(): View|RedirectResponse
    {
        $orderId = session('last_order_id');
        if (! $orderId) {
            return redirect()->route('shop.index')->with('info', 'No order to display.');
        }

        $order = Order::with('items')->find($orderId);
        if (! $order) {
            return redirect()->route('shop.index')->with('error', 'Order not found.');
        }

        if ($order->user_id && $order->user_id !== auth()->id()) {
            return redirect()->route('home')->with('error', 'Order not found.');
        }

        return view('orders.success', compact('order'));
    }
}
