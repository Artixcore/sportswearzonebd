<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->get('period', 'month'); // day, week, month
        $date = $request->get('date', now()->toDateString());
        $start = match ($period) {
            'day' => $date,
            'week' => now()->parse($date)->startOfWeek()->toDateString(),
            'month' => now()->parse($date)->startOfMonth()->toDateString(),
            default => now()->startOfMonth()->toDateString(),
        };
        $end = match ($period) {
            'day' => $date,
            'week' => now()->parse($date)->endOfWeek()->toDateString(),
            'month' => now()->parse($date)->endOfMonth()->toDateString(),
            default => now()->endOfMonth()->toDateString(),
        };

        $ordersQuery = Order::where('status', '!=', 'cancelled')->whereBetween('created_at', [$start, $end . ' 23:59:59']);
        $salesQuery = Sale::whereBetween('created_at', [$start, $end . ' 23:59:59']);

        $ordersTotal = (clone $ordersQuery)->sum('total');
        $posTotal = (clone $salesQuery)->sum('total');
        $totalRevenue = $ordersTotal + $posTotal;
        $ordersCount = (clone $ordersQuery)->count();
        $salesCount = (clone $salesQuery)->count();

        $ordersSummary = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$start, $end . ' 23:59:59'])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $profitEstimate = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('orders.created_at', [$start, $end . ' 23:59:59'])
            ->selectRaw('SUM((order_items.price - COALESCE(order_items.cost, 0)) * order_items.quantity) as profit')
            ->value('profit') ?? 0;
        $profitEstimate += DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$start, $end . ' 23:59:59'])
            ->selectRaw('SUM((sale_items.price - COALESCE(sale_items.cost, 0)) * sale_items.quantity - sale_items.discount) as profit')
            ->value('profit') ?? 0;

        return view('admin.reports.index', compact(
            'period', 'date', 'start', 'end',
            'totalRevenue', 'ordersCount', 'salesCount', 'ordersSummary',
            'profitEstimate'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse|RedirectResponse
    {
        $start = $request->get('date_from', now()->startOfMonth()->toDateString());
        $end = $request->get('date_to', now()->toDateString());

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales-report-' . $start . '-to-' . $end . '.csv"',
        ];

        try {
            return response()->stream(function () use ($start, $end) {
                $handle = fopen('php://output', 'w');
                if ($handle === false) {
                    throw new \RuntimeException('Failed to open output stream');
                }
                fputcsv($handle, ['Date', 'Type', 'ID', 'Total', 'Status']);
                Order::where('status', '!=', 'cancelled')->whereBetween('created_at', [$start, $end . ' 23:59:59'])
                    ->orderBy('created_at')
                    ->get(['id', 'created_at', 'total', 'status'])
                    ->each(function ($o) use ($handle) {
                        fputcsv($handle, [$o->created_at->toDateString(), 'Order', $o->id, $o->total, $o->status]);
                    });
                Sale::whereBetween('created_at', [$start, $end . ' 23:59:59'])
                    ->orderBy('created_at')
                    ->get(['id', 'created_at', 'total', 'status'])
                    ->each(function ($s) use ($handle) {
                        fputcsv($handle, [$s->created_at->toDateString(), 'POS', $s->id, $s->total, $s->status]);
                    });
                fclose($handle);
            }, 200, $headers);
        } catch (Throwable $e) {
            Log::error('Report CSV export failed', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return redirect()->route('admin.reports.index')->with('error', 'Failed to export report. Please try again.');
        }
    }
}
