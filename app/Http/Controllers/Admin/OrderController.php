<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\ActivityLog;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with('user', 'customer');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('id', $request->search)
                    ->orWhere('shipping_name', 'like', '%' . $request->search . '%')
                    ->orWhere('guest_email', 'like', '%' . $request->search . '%');
            });
        }
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $orders = $query->paginate(15)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load('items.product', 'items.productVariant', 'user', 'customer');
        return view('admin.orders.show', compact('order'));
    }

    public function invoice(Order $order): View
    {
        $order->load('items.product', 'items.productVariant', 'user', 'customer');
        return view('admin.orders.invoice', compact('order'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $oldStatus = $order->status;
        $order->update([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);
        ActivityLog::log('order.status_updated', "Order #{$order->id} status: {$oldStatus} → {$request->status}", $order);
        return redirect()->back()->with('success', 'Order status updated.');
    }
}
