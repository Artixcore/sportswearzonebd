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
        $query = Product::where('is_active', true)->with(['images', 'category']);

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
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
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
        $category = $request->filled('category') ? Category::where('slug', $request->category)->first() : null;

        return view('products.index', compact('products', 'categories', 'category'));
    }

    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = $category->products()->where('is_active', true)->with(['images', 'category'])->orderBy('sort_order')->paginate(12)->withQueryString();
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->get();

        return view('products.index', compact('products', 'categories', 'category'));
    }
}
