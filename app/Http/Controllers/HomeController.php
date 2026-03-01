<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->with(['images', 'category.parent'])
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        $newArrivals = Product::where('is_active', true)
            ->with(['images', 'category.parent'])
            ->latest()
            ->limit(8)
            ->get();

        $categories = Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->get();

        return view('home', compact('featuredProducts', 'newArrivals', 'categories'));
    }
}
