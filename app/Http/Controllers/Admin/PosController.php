<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PosCheckoutRequest;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $customers = Customer::orderBy('name')->get();
        return view('admin.pos.index', compact('customers'));
    }

    public function searchProducts(Request $request): JsonResponse
    {
        $q = $request->get('q', '');
        $products = Product::where('is_active', true)
            ->with('variants')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%')
                    ->orWhere('sku', 'like', '%' . $q . '%');
            })
            ->limit(20)
            ->get();
        return response()->json($products);
    }

    public function checkout(PosCheckoutRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $orderDiscount = (float) ($validated['order_discount'] ?? 0);
        $subtotal = 0;
        $items = [];

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $variant = isset($item['product_variant_id']) ? \App\Models\ProductVariant::find($item['product_variant_id']) : null;
                $qty = (int) $item['quantity'];
                $price = (float) $item['price'];
                $discount = (float) ($item['discount'] ?? 0);
                $cost = $product->cost_price ?? 0;
                $name = $variant ? $product->name . ' - ' . $variant->name : $product->name;
                $sku = $variant ? $variant->sku : $product->sku;
                $lineTotal = $price * $qty - $discount;
                $subtotal += $lineTotal;

                if ($variant) {
                    if ($variant->stock < $qty) {
                        DB::rollBack();
                        return response()->json(['message' => 'Insufficient stock for ' . $name], 422);
                    }
                    $variant->decrement('stock', $qty);
                    InventoryLog::create([
                        'product_id' => $product->id,
                        'product_variant_id' => $variant->id,
                        'type' => InventoryLog::TYPE_OUT,
                        'quantity' => -$qty,
                        'reference_type' => Sale::class,
                        'user_id' => auth()->id(),
                    ]);
                } else {
                    if ($product->stock < $qty) {
                        DB::rollBack();
                        return response()->json(['message' => 'Insufficient stock for ' . $name], 422);
                    }
                    $product->decrement('stock', $qty);
                    InventoryLog::create([
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                        'type' => InventoryLog::TYPE_OUT,
                        'quantity' => -$qty,
                        'reference_type' => Sale::class,
                        'user_id' => auth()->id(),
                    ]);
                }

                $items[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'name' => $name,
                    'sku' => $sku,
                    'price' => $price,
                    'cost' => $cost,
                    'quantity' => $qty,
                    'discount' => $discount,
                ];
            }

            $total = $subtotal - $orderDiscount;
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'status' => 'completed',
                'subtotal' => $subtotal,
                'discount' => $orderDiscount,
                'tax' => 0,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $item['sale_id'] = $sale->id;
                SaleItem::create($item);
            }

            ActivityLog::log('pos.sale', 'POS Sale #' . $sale->id . ' - ৳' . number_format($sale->total, 0), $sale);
            DB::commit();
            return response()->json(['sale_id' => $sale->id, 'redirect' => route('admin.pos.receipt', $sale)]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function receipt(Sale $sale): View
    {
        $sale->load('items', 'customer', 'user');
        return view('admin.pos.receipt', compact('sale'));
    }
}
