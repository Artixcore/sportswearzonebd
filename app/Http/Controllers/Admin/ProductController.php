<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category', 'images')->withCount('variants');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereRaw('stock <= low_stock_threshold');
            } elseif ($request->stock_status === 'in_stock') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock', '=', 0);
            }
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['stock'] = (int) ($validated['stock'] ?? 0);
        $validated['low_stock_threshold'] = (int) ($validated['low_stock_threshold'] ?? 5);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $product = Product::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store('products/' . $product->id, 'public');
                $product->images()->create(['path' => $path, 'sort_order' => $i]);
            }
        }

        ActivityLog::log('product.created', 'Product created: ' . $product->name, $product);
        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $product->load('images', 'variants');
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['stock'] = (int) ($validated['stock'] ?? 0);
        $validated['low_stock_threshold'] = (int) ($validated['low_stock_threshold'] ?? 5);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['updated_by'] = auth()->id();

        $product->update($validated);

        if ($request->hasFile('images')) {
            $start = $product->images()->max('sort_order') + 1;
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store('products/' . $product->id, 'public');
                $product->images()->create(['path' => $path, 'sort_order' => $start + $i]);
            }
        }

        ActivityLog::log('product.updated', 'Product updated: ' . $product->name, $product);
        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;
        $product->delete();
        ActivityLog::log('product.deleted', 'Product deleted: ' . $name);
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }
}
