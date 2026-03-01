<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::where('is_active', true)->with(['images', 'category.parent']);

        if ($request->filled('category')) {
            $cat = Category::where('slug', $request->category)->with('children')->first();
            if ($cat) {
                if ($cat->parent_id !== null) {
                    $query->where('category_id', $cat->id);
                } else {
                    $categoryIds = array_merge([$cat->id], $cat->children->pluck('id')->all());
                    $query->whereIn('category_id', $categoryIds);
                }
            }
        }
        if ($request->filled('sort')) {
            match ($request->sort) {
                'price_asc' => $query->orderBy('price', 'asc'),
                'price_desc' => $query->orderBy('price', 'desc'),
                'newest' => $query->latest(),
                default => $query->orderBy('sort_order')->orderBy('id'),
            };
        } else {
            $query->orderBy('sort_order')->orderBy('id');
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->orderBy('name')->get();
        $category = $request->filled('category') ? Category::where('slug', $request->category)->first() : null;

        return view('products.index', compact('products', 'categories', 'category'));
    }

    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->with('children')->firstOrFail();
        $categoryIds = $category->parent_id === null
            ? array_merge([$category->id], $category->children->pluck('id')->all())
            : [$category->id];
        $products = Product::where('is_active', true)
            ->whereIn('category_id', $categoryIds)
            ->with(['images', 'category.parent'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString();
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'category'));
    }
}
