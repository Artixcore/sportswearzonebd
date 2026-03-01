<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }
        $productIds = array_keys($cart);
        $products = Product::with('images')->whereIn('id', $productIds)->get()->keyBy('id');
        $cartItems = [];
        $subtotal = 0;
        foreach ($cart as $id => $qty) {
            if ($products->has($id)) {
                $p = $products[$id];
                $qty = (int) $qty;
                $cartItems[] = (object) ['product' => $p, 'quantity' => $qty];
                $subtotal += $p->price * $qty;
            }
        }
        $shipping = 0;
        $tax = 0;
        $total = $subtotal + $shipping + $tax;

        $initiateEventId = Str::uuid()->toString();
        $capi = app(\App\Services\MetaConversionsApiService::class);
        if ($capi->isConfigured()) {
            $contentIds = array_map(fn ($i) => $i->product->id, $cartItems);
            $userData = auth()->user() ? ['em' => auth()->user()->email] : [];
            $capi->sendInitiateCheckout($total, $contentIds, $initiateEventId, $userData);
        }

        return view('checkout.index', compact('cartItems', 'subtotal', 'shipping', 'tax', 'total', 'initiateEventId'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Your cart is empty.'], 422);
            }
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:255',
            'phone' => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],
            'city' => 'required|string|max:255',
            'address' => 'required|string|min:10',
            'email' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        session(['checkout_customer' => $validated]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'redirect' => route('checkout.confirm'),
            ]);
        }

        return redirect()->route('checkout.confirm')->with('success', 'Please review your order.');
    }

    public function confirm(): View|RedirectResponse
    {
        $cart = session('cart', []);
        $customer = session('checkout_customer');

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        if (empty($customer)) {
            return redirect()->route('checkout.index')->with('error', 'Please enter your details first.');
        }

        $productIds = array_keys($cart);
        $products = Product::with('images')->whereIn('id', $productIds)->get()->keyBy('id');
        $cartItems = [];
        $subtotal = 0;
        foreach ($cart as $id => $qty) {
            if ($products->has($id)) {
                $p = $products[$id];
                $qty = (int) $qty;
                $cartItems[] = (object) ['product' => $p, 'quantity' => $qty];
                $subtotal += $p->price * $qty;
            }
        }
        $shipping = 0;
        $tax = 0;
        $total = $subtotal + $shipping + $tax;

        return view('checkout.confirm', compact('cartItems', 'subtotal', 'shipping', 'tax', 'total', 'customer'));
    }

    public function success(Request $request, int $orderId): View|RedirectResponse
    {
        $order = \App\Models\Order::with('items')->findOrFail($orderId);
        if ($order->user_id && $order->user_id !== auth()->id()) {
            if (session('last_order_id') != $order->id) {
                return redirect()->route('home')->with('error', 'Order not found.');
            }
        }

        return redirect()->route('orders.success')->with('last_order_id', $order->id);
    }
}
