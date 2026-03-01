<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InventoryAdjustRequest;
use App\Models\InventoryLog;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = InventoryLog::with('product', 'productVariant', 'user')->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get();
        return view('admin.inventory.index', compact('logs', 'products'));
    }

    public function adjust(): View
    {
        $products = Product::with('variants')->orderBy('name')->get();
        $variantsByProduct = $products->keyBy('id')->map(fn ($p) => $p->variants->map(fn ($v) => ['id' => $v->id, 'name' => $v->name, 'stock' => $v->stock])->values());
        return view('admin.inventory.adjust', compact('products', 'variantsByProduct'));
    }

    public function storeAdjust(InventoryAdjustRequest $request): RedirectResponse
    {
        $product = Product::findOrFail($request->product_id);
        $variant = $request->product_variant_id ? \App\Models\ProductVariant::find($request->product_variant_id) : null;
        $quantity = (int) $request->quantity;
        $type = $request->type;

        if ($type === 'out' && $quantity > 0) {
            $quantity = -$quantity;
        }
        if ($type === 'in' && $quantity < 0) {
            $quantity = abs($quantity);
        }
        if ($type === 'adjustment') {
            // quantity can be +/- for adjustment
        }

        InventoryLog::create([
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'type' => $type,
            'quantity' => $quantity,
            'notes' => $request->notes,
            'user_id' => auth()->id(),
        ]);

        if ($variant) {
            $variant->increment('stock', $quantity);
        } else {
            $product->increment('stock', $quantity);
        }

        \App\Models\ActivityLog::log('inventory.adjustment', 'Inventory adjustment: ' . ($variant ? $variant->name : $product->name) . ' ' . ($quantity >= 0 ? '+' : '') . $quantity);
        return redirect()->route('admin.inventory.index')->with('success', 'Inventory adjusted.');
    }

    public function productHistory(Product $product): View
    {
        $product->load('variants');
        $logs = InventoryLog::where('product_id', $product->id)->with('productVariant', 'user')->latest()->paginate(30);
        return view('admin.inventory.history', compact('product', 'logs'));
    }
}
