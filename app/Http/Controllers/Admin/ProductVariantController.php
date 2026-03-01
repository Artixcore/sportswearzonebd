<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVariantRequest;
use App\Http\Requests\Admin\UpdateVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductVariantController extends Controller
{
    public function index(Product $product): View
    {
        $product->load('variants');
        return view('admin.products.variants.index', compact('product'));
    }

    public function create(Product $product): View
    {
        return view('admin.products.variants.create', compact('product'));
    }

    public function store(StoreVariantRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();
        $validated['product_id'] = $product->id;
        $validated['low_stock_threshold'] = (int) ($validated['low_stock_threshold'] ?? 5);
        ProductVariant::create($validated);
        return redirect()->route('admin.products.variants.index', $product)->with('success', 'Variant created.');
    }

    public function edit(Product $product, ProductVariant $variant): View
    {
        return view('admin.products.variants.edit', compact('product', 'variant'));
    }

    public function update(UpdateVariantRequest $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $validated = $request->validated();
        $validated['low_stock_threshold'] = (int) ($validated['low_stock_threshold'] ?? 5);
        $variant->update($validated);
        return redirect()->route('admin.products.variants.index', $product)->with('success', 'Variant updated.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        $variant->delete();
        return redirect()->route('admin.products.variants.index', $product)->with('success', 'Variant deleted.');
    }
}
