<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->with(['images', 'category'])->firstOrFail();
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with('images')
            ->limit(4)
            ->get();

        $viewContentEventId = \Illuminate\Support\Str::uuid()->toString();
        $capi = app(\App\Services\MetaConversionsApiService::class);
        if ($capi->isConfigured()) {
            $capi->sendViewContent($product, $viewContentEventId);
        }

        $seoService = app(\App\Services\SeoService::class);
        $breadcrumbJsonLd = $seoService->breadcrumbJsonLd([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Shop', 'url' => route('shop.index')],
            ['name' => $product->name],
        ]);

        return view('products.show', compact('product', 'related', 'viewContentEventId', 'breadcrumbJsonLd'));
    }
}
