<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
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

    public function add(AddToCartRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $id = (int) $validated['product_id'];
        $qty = (int) ($validated['quantity'] ?? 1);
        $cart = session('cart', []);
        $cart[$id] = ($cart[$id] ?? 0) + $qty;
        session(['cart' => $cart]);

        $product = Product::find($id);
        $eventId = \Illuminate\Support\Str::uuid()->toString();
        $capi = app(\App\Services\MetaConversionsApiService::class);
        if ($capi->isConfigured() && $product) {
            $capi->sendAddToCart($product, $qty, $eventId);
        }

        if ($request->wantsJson() || $request->ajax()) {
            $cartCount = (int) array_sum(session('cart', []));
            $data = [
                'status' => 'success',
                'message' => 'Added to cart.',
                'cart_count' => $cartCount,
            ];
            if ($request->input('redirect') === 'checkout') {
                $data['redirect'] = route('checkout.index');
            }
            return response()->json($data);
        }

        if ($request->input('redirect') === 'checkout') {
            return redirect()->route('checkout.index')->with('success', 'Added to cart.');
        }

        return redirect()->back()->with('success', 'Added to cart.');
    }

    public function update(UpdateCartRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $cart = session('cart', []);
        $id = (int) $validated['product_id'];
        $qty = (int) $validated['quantity'];
        $product = Product::find($id);
        $removed = false;

        if ($qty <= 0) {
            unset($cart[$id]);
            $removed = true;
        } else {
            $cart[$id] = $qty;
        }
        session(['cart' => $cart]);

        $totals = $this->cartTotals(session('cart', []));
        $itemSubtotal = 0;
        if ($product && ! $removed) {
            $itemSubtotal = (float) $product->price * $qty;
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'cart_total' => $totals['total'],
                'item_subtotal' => round($itemSubtotal, 2),
                'cart_count' => $totals['count'],
                'removed' => $removed,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function remove(int $productId, Request $request): JsonResponse|RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[$productId]);
        session(['cart' => $cart]);

        if ($request->wantsJson() || $request->ajax()) {
            $totals = $this->cartTotals(session('cart', []));
            return response()->json([
                'status' => 'success',
                'cart_total' => $totals['total'],
                'cart_count' => $totals['count'],
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Item removed.');
    }

    public function clear(Request $request): JsonResponse|RedirectResponse
    {
        session()->forget('cart');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'cart_count' => 0]);
        }

        return redirect()->route('cart.index')->with('success', 'Cart cleared.');
    }

    private function cartTotals(array $cart): array
    {
        if (empty($cart)) {
            return ['total' => 0, 'count' => 0];
        }
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $total = 0;
        foreach ($cart as $id => $qty) {
            if ($products->has($id)) {
                $total += (float) $products[$id]->price * (int) $qty;
            }
        }
        return [
            'total' => round($total, 2),
            'count' => (int) array_sum($cart),
        ];
    }
}
