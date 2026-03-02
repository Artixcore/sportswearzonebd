<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = now()->toDateString();
        $todayOrdersCount = Order::whereDate('created_at', $today)->where('status', '!=', 'cancelled')->count();
        $todaySalesTotal = Order::whereDate('created_at', $today)->where('status', '!=', 'cancelled')->sum('total');
        $todayPosTotal = Sale::whereDate('created_at', $today)->sum('total');
        $todaySalesTotal += $todayPosTotal;

        $lowStockCount = Product::whereRaw('stock <= low_stock_threshold')->count()
            + \App\Models\ProductVariant::whereRaw('stock <= low_stock_threshold')->count();
        $pendingOrdersCount = Order::whereIn('status', ['pending', 'confirmed', 'processing'])->count();

        $topSellingProducts = OrderItem::query()
            ->selectRaw('product_id, sum(quantity) as total_qty')
            ->whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->with('product')
            ->get();

        $recentActivity = ActivityLog::with('user')->latest()->limit(15)->get();

        $recentOrders = Order::with('user', 'customer')->latest()->limit(10)->get();

        return view('admin.dashboard', compact(
            'todayOrdersCount',
            'todaySalesTotal',
            'lowStockCount',
            'pendingOrdersCount',
            'topSellingProducts',
            'recentActivity',
            'recentOrders'
        ));
    }

    public function newOrdersCheck(): JsonResponse
    {
        $latest = Order::orderByDesc('id')->first();
        $pendingCount = Order::whereIn('status', ['pending', 'confirmed', 'processing'])->count();

        return response()->json([
            'latest_order_id' => $latest ? (int) $latest->id : 0,
            'latest_at' => $latest ? $latest->created_at->toIso8601String() : null,
            'pending_count' => $pendingCount,
        ]);
    }
}
