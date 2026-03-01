<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart = session('cart', []);
        $productIds = array_keys($cart);
        $products = Product::with('images')->whereIn('id', $productIds)->get()->keyBy('id');
        $cartItems = [];
        foreach ($cart as $id => $qty) {
            if ($products->has($id)) {
                $cartItems[] = (object) ['product' => $products[$id], 'quantity' => (int) $qty];
            }
        }

        return view('cart.index', compact('cartItems'));
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate(['product_id' => 'required|exists:products,id', 'quantity' => 'nullable|integer|min:1']);
        $id = (int) $request->product_id;
        $qty = (int) ($request->quantity ?? 1);
        $cart = session('cart', []);
        $cart[$id] = ($cart[$id] ?? 0) + $qty;
        session(['cart' => $cart]);

        $product = Product::find($id);
        $eventId = \Illuminate\Support\Str::uuid()->toString();
        $capi = app(\App\Services\MetaConversionsApiService::class);
        if ($capi->isConfigured() && $product) {
            $capi->sendAddToCart($product, $qty, $eventId);
        }

        if ($request->input('redirect') === 'checkout') {
            return redirect()->route('checkout.index')->with('success', 'Added to cart.');
        }

        return redirect()->back()->with('success', 'Added to cart.');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);
        $cart = session('cart', []);
        $id = (int) $request->product_id;
        $qty = (int) $request->quantity;
        if ($qty <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id] = $qty;
        }
        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function remove(int $productId): RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[$productId]);
        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', 'Item removed.');
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Cart cleared.');
    }
}
