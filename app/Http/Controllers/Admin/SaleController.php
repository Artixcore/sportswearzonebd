<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sale::with('user', 'customer');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        $query->latest();

        $sales = $query->paginate(20)->withQueryString();
        return view('admin.sales.index', compact('sales'));
    }

    public function show(Sale $sale): View
    {
        $sale->load('items.product', 'items.productVariant', 'customer', 'user');
        return view('admin.sales.show', compact('sale'));
    }
}
