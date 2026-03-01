<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\ActivityLog;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

    public function updateDeliveryAdvance(Request $request, Order $order): JsonResponse
    {
        $hasAdvance = $order->delivery_advance_customer_confirmed || (isset($order->delivery_advance_paid) && (float) $order->delivery_advance_paid > 0);
        if (! $hasAdvance) {
            return response()->json([
                'status' => 'error',
                'message' => 'This order does not have a delivery charge advance to verify.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'delivery_advance_admin_txn_id' => 'required|string|min:1',
            'delivery_advance_admin_verified' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $order->update([
            'delivery_advance_admin_txn_id' => $request->delivery_advance_admin_txn_id,
            'delivery_advance_admin_verified' => $request->boolean('delivery_advance_admin_verified'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Delivery advance verification updated.',
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse|RedirectResponse
    {
        $newStatus = $request->status;
        $restrictedStatuses = ['confirmed', 'processing', 'shipped'];
        $hasAdvance = $order->delivery_advance_customer_confirmed || (isset($order->delivery_advance_paid) && (float) $order->delivery_advance_paid > 0);

        if ($hasAdvance && in_array($newStatus, $restrictedStatuses, true)) {
            $adminTxnId = $order->delivery_advance_admin_txn_id;
            $adminVerified = $order->delivery_advance_admin_verified;
            if (empty(trim((string) $adminTxnId)) || ! $adminVerified) {
                $message = 'Please set Delivery Advance Admin Transaction ID and mark as verified before confirming/processing/shipping.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['status' => 'error', 'message' => $message], 422);
                }
                return redirect()->back()->with('error', $message);
            }
        }

        $oldStatus = $order->status;
        $order->update([
            'status' => $newStatus,
            'updated_by' => auth()->id(),
        ]);
        ActivityLog::log('order.status_updated', "Order #{$order->id} status: {$oldStatus} → {$newStatus}", $order);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Order status updated.',
            ]);
        }

        return redirect()->back()->with('success', 'Order status updated.');
    }
}
