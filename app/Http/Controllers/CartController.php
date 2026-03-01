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
    /**
     * Parse cart key "productId_size" into [productId, size]. Supports legacy key "productId".
     */
    private function parseCartKey(string $key): array
    {
        $key = (string) $key;
        $lastUnderscore = strrpos($key, '_');
        if ($lastUnderscore === false) {
            return [(int) $key, null];
        }
        return [(int) substr($key, 0, $lastUnderscore), substr($key, $lastUnderscore + 1)];
    }

    /**
     * Build cart key from product id and size.
     */
    private function cartKey(int $productId, string $size): string
    {
        return $productId . '_' . $size;
    }

    public function index(): View
    {
        $cart = session('cart', []);
        $cartItems = [];
        $productIds = [];
        foreach (array_keys($cart) as $k) {
            [$id] = $this->parseCartKey((string) $k);
            $productIds[$id] = true;
        }
        $productIds = array_keys($productIds);
        $products = Product::with('images')->whereIn('id', $productIds)->get()->keyBy('id');
        foreach ($cart as $key => $qty) {
            [$id, $size] = $this->parseCartKey((string) $key);
            if ($products->has($id)) {
                $cartItems[] = (object) [
                    'product' => $products[$id],
                    'quantity' => (int) $qty,
                    'size' => $size ?? '',
                    'cart_key' => (string) $key,
                ];
            }
        }

        return view('cart.index', compact('cartItems'));
    }

    public function add(AddToCartRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $id = (int) $validated['product_id'];
        $size = (string) $validated['size'];
        $qty = (int) ($validated['quantity'] ?? 1);
        $cart = session('cart', []);
        $key = $this->cartKey($id, $size);
        $cart[$key] = ($cart[$key] ?? 0) + $qty;
        session(['cart' => $cart]);

        $product = Product::find($id);
        $eventId = \Illuminate\Support\Str::uuid()->toString();
        $capi = app(\App\Services\MetaConversionsApiService::class);
        if ($capi->isConfigured() && $product) {
            $capi->sendAddToCart($product, $qty, $eventId);
        }

        $totals = $this->cartTotals(session('cart', []));
        if ($request->wantsJson() || $request->ajax()) {
            $data = [
                'status' => 'success',
                'message' => 'Added to cart.',
                'cart_count' => $totals['count'],
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
        $size = (string) $validated['size'];
        $qty = (int) $validated['quantity'];
        $key = $this->cartKey($id, $size);
        $product = Product::find($id);
        $removed = false;

        if ($qty <= 0) {
            unset($cart[$key]);
            $removed = true;
        } else {
            $cart[$key] = $qty;
        }
        session(['cart' => $cart]);

        $totals = $this->cartTotals(session('cart', []));
        $itemSubtotal = 0;
        if ($product && ! $removed) {
            $itemSubtotal = (float) $product->final_price * $qty;
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

    public function remove(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|integer|min:1',
            'size' => 'nullable|string|max:32',
        ]);
        $cart = session('cart', []);
        $id = (int) $request->product_id;
        $size = (string) ($request->size ?? '');
        $key = $this->cartKey($id, $size);
        unset($cart[$key]);
        if ($size === '') {
            unset($cart[$id]);
            unset($cart[(string) $id]);
        }
        session(['cart' => $cart]);

        $totals = $this->cartTotals(session('cart', []));
        if ($request->wantsJson() || $request->ajax()) {
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
        $productIds = [];
        foreach (array_keys($cart) as $k) {
            [$id] = $this->parseCartKey((string) $k);
            $productIds[$id] = true;
        }
        $products = Product::whereIn('id', array_keys($productIds))->get()->keyBy('id');
        $total = 0;
        $count = 0;
        foreach ($cart as $key => $qty) {
            [$id] = $this->parseCartKey((string) $key);
            if ($products->has($id)) {
                $total += (float) $products[$id]->final_price * (int) $qty;
                $count += (int) $qty;
            }
        }
        return [
            'total' => round($total, 2),
            'count' => $count,
        ];
    }
}
