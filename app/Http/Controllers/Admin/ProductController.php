<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        $rootCategories = Category::whereNull('parent_id')->orderBy('sort_order')->orderBy('name')->get();
        $subcategoriesByParentId = Category::whereNotNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('parent_id')
            ->map(fn ($group) => $group->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values()->all())
            ->toArray();
        $selectedParentId = null;
        $selectedSubcategoryId = null;
        if (old('category_id')) {
            $cat = Category::find(old('category_id'));
            if ($cat) {
                if ($cat->parent_id) {
                    $selectedParentId = $cat->parent_id;
                    $selectedSubcategoryId = $cat->id;
                } else {
                    $selectedParentId = $cat->id;
                }
            }
        }
        return view('admin.products.create', compact('rootCategories', 'subcategoriesByParentId', 'selectedParentId', 'selectedSubcategoryId'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['main_image'], $validated['gallery_images']);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['stock'] = (int) ($validated['stock'] ?? 0);
        $validated['low_stock_threshold'] = (int) ($validated['low_stock_threshold'] ?? 5);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $uploadedPaths = [];

        try {
            DB::transaction(function () use ($validated, $request, &$uploadedPaths) {
                $product = Product::create($validated);

                $mainDir = config('filesystems.product_paths.main_dir', 'products/main');
                $galleryDir = config('filesystems.product_paths.gallery_dir', 'products/gallery');
                $disk = Storage::disk('public');

                $mainFile = $request->file('main_image');
                $mainPath = $disk->putFileAs(
                    $mainDir,
                    $mainFile,
                    $this->uniqueFilename($mainFile)
                );
                $uploadedPaths[] = $mainPath;
                $product->update(['main_image_path' => $mainPath]);

                if ($request->hasFile('gallery_images')) {
                    $sortOrder = 0;
                    foreach ($request->file('gallery_images') as $file) {
                        $path = $disk->putFileAs($galleryDir, $file, $this->uniqueFilename($file));
                        $uploadedPaths[] = $path;
                        $product->images()->create(['path' => $path, 'sort_order' => $sortOrder++]);
                    }
                }

                ActivityLog::log('product.created', 'Product created: ' . $product->name, $product);
            });
        } catch (\Throwable $e) {
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $product->load('images', 'variants', 'category.parent');
        $rootCategories = Category::whereNull('parent_id')->orderBy('sort_order')->orderBy('name')->get();
        $subcategoriesByParentId = Category::whereNotNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('parent_id')
            ->map(fn ($group) => $group->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values()->all())
            ->toArray();
        $selectedParentId = null;
        $selectedSubcategoryId = null;
        if ($product->category_id && $product->category) {
            if ($product->category->parent_id) {
                $selectedParentId = $product->category->parent_id;
                $selectedSubcategoryId = $product->category_id;
            } else {
                $selectedParentId = $product->category_id;
            }
        }
        return view('admin.products.edit', compact('product', 'rootCategories', 'subcategoriesByParentId', 'selectedParentId', 'selectedSubcategoryId'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['main_image'], $validated['gallery_images']);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['stock'] = (int) ($validated['stock'] ?? 0);
        $validated['low_stock_threshold'] = (int) ($validated['low_stock_threshold'] ?? 5);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['updated_by'] = auth()->id();

        $uploadedPaths = [];

        try {
            DB::transaction(function () use ($validated, $request, $product, &$uploadedPaths) {
                $mainDir = config('filesystems.product_paths.main_dir', 'products/main');
                $galleryDir = config('filesystems.product_paths.gallery_dir', 'products/gallery');
                $disk = Storage::disk('public');

                if ($request->hasFile('main_image')) {
                    if ($product->main_image_path) {
                        $disk->delete($product->main_image_path);
                    }
                    $mainFile = $request->file('main_image');
                    $mainPath = $disk->putFileAs($mainDir, $mainFile, $this->uniqueFilename($mainFile));
                    $uploadedPaths[] = $mainPath;
                    $validated['main_image_path'] = $mainPath;
                }

                $product->update($validated);

                if ($request->hasFile('gallery_images')) {
                    $start = $product->images()->max('sort_order') + 1;
                    foreach ($request->file('gallery_images') as $i => $file) {
                        $path = $disk->putFileAs($galleryDir, $file, $this->uniqueFilename($file));
                        $uploadedPaths[] = $path;
                        $product->images()->create(['path' => $path, 'sort_order' => $start + $i]);
                    }
                }

                ActivityLog::log('product.updated', 'Product updated: ' . $product->name, $product);
            });
        } catch (\Throwable $e) {
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;
        $disk = Storage::disk('public');
        try {
            if ($product->main_image_path) {
                $disk->delete($product->main_image_path);
            }
            foreach ($product->images as $image) {
                $disk->delete($image->path);
            }
        } catch (\Throwable $e) {
            report($e);
        }
        $product->delete();
        ActivityLog::log('product.deleted', 'Product deleted: ' . $name);
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    public function destroyImage(Product $product, ProductImage $image): JsonResponse
    {
        if ($image->product_id !== $product->id) {
            return response()->json(['message' => 'Image does not belong to this product.'], 403);
        }
        try {
            Storage::disk('public')->delete($image->path);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Failed to delete file from storage.'], 500);
        }
        $image->delete();
        return response()->json(['success' => true, 'message' => 'Gallery image deleted.']);
    }

    private function uniqueFilename(\Illuminate\Http\UploadedFile $file): string
    {
        $ext = $file->getClientOriginalExtension() ?: $file->guessExtension();
        return Str::uuid()->toString() . '_' . time() . '.' . strtolower($ext);
    }
}
