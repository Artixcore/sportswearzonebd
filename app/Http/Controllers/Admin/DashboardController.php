<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $revenue = Order::where('status', '!=', 'cancelled')->sum('total');
        $ordersCount = Order::count();
        $productsCount = Product::count();
        $customersCount = User::where('is_admin', false)->count();
        $recentOrders = Order::with('user')->latest()->limit(10)->get();

        return view('admin.dashboard', compact('revenue', 'ordersCount', 'productsCount', 'customersCount', 'recentOrders'));
    }
}
