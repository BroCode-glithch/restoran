<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $businessId = currentBusinessId();
        $today = Carbon::today();
        $completedOrders = Order::query()
            ->where('business_id', $businessId)
            ->where('status', 'completed');

        $revenueToday = (clone $completedOrders)->whereDate('updated_at', $today)->sum('total');
        $ordersToday = (clone $completedOrders)->whereDate('updated_at', $today)->count();
        $revenueThisWeek = $this->buildSumTrendSeries($completedOrders, 'total', 7, 'updated_at')['values'];
        $revenueLast7Days = array_sum($revenueThisWeek);
        $averageTicket = $ordersToday > 0 ? $revenueToday / $ordersToday : 0;
        $paymentBreakdown = $this->buildPaymentBreakdown($completedOrders);
        $statusMix = $this->buildStatusBreakdown($businessId);
        $bestSellers = $this->buildTopSellers($businessId);
        $slowMovers = $this->buildSlowMovingProducts($businessId);

        return view('dashboards.reports', [
            'revenueToday' => $revenueToday,
            'ordersToday' => $ordersToday,
            'revenueLast7Days' => $revenueLast7Days,
            'averageTicket' => $averageTicket,
            'paymentBreakdown' => $paymentBreakdown,
            'statusMix' => $statusMix,
            'bestSellers' => $bestSellers,
            'slowMovers' => $slowMovers,
            'trendSeries' => $this->buildSumTrendSeries($completedOrders, 'total', 7, 'updated_at'),
            'chartOptions' => [
                'payment' => [
                    'labels' => $paymentBreakdown['labels'],
                    'values' => $paymentBreakdown['values'],
                    'colors' => $paymentBreakdown['colors'],
                ],
                'status' => [
                    'labels' => $statusMix['labels'],
                    'values' => $statusMix['values'],
                    'colors' => $statusMix['colors'],
                ],
            ],
        ]);
    }

    protected function buildSumTrendSeries($query, $column, $days = 7, $dateColumn = 'created_at')
    {
        $labels = [];
        $values = [];

        for ($offset = $days - 1; $offset >= 0; $offset--) {
            $date = Carbon::today()->subDays($offset);
            $labels[] = $date->format('D');
            $values[] = (float) (clone $query)->whereDate($dateColumn, $date)->sum($column);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    protected function buildPaymentBreakdown($completedOrders)
    {
        $methods = [
            'cash_on_delivery' => 'Cash',
            'bank_transfer' => 'Bank transfer',
            'demo_card' => 'Card',
        ];

        $rows = (clone $completedOrders)
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total) as amount'))
            ->groupBy('payment_method')
            ->get();

        $labels = [];
        $values = [];
        $colors = [];

        foreach ($rows as $row) {
            $labels[] = $methods[$row->payment_method] ?? Str::title(str_replace('_', ' ', $row->payment_method));
            $values[] = (int) $row->count;
            $colors[] = $this->paymentColor($row->payment_method);
        }

        if (empty($labels)) {
            return [
                'labels' => ['No payments yet'],
                'values' => [1],
                'colors' => ['rgba(148, 163, 184, 0.9)'],
            ];
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'colors' => $colors,
        ];
    }

    protected function paymentColor($method)
    {
        $palette = [
            'cash_on_delivery' => 'rgba(22, 163, 74, 0.85)',
            'bank_transfer' => 'rgba(59, 130, 246, 0.85)',
            'demo_card' => 'rgba(234, 88, 12, 0.85)',
        ];

        return $palette[$method] ?? 'rgba(148, 163, 184, 0.85)';
    }

    protected function buildStatusBreakdown($businessId)
    {
        $statuses = ['placed', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'completed', 'cancelled'];
        $labels = [];
        $values = [];
        $colors = [];

        foreach ($statuses as $status) {
            $labels[] = orderStatusLabel($status);
            $values[] = Order::query()
                ->where('business_id', $businessId)
                ->where('status', $status)
                ->count();
            $colors[] = $this->statusColor($status);
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'colors' => $colors,
        ];
    }

    protected function statusColor($status)
    {
        $palette = [
            'placed' => 'rgba(100, 116, 139, 0.95)',
            'confirmed' => 'rgba(254, 161, 22, 0.88)',
            'preparing' => 'rgba(15, 23, 43, 0.86)',
            'ready' => 'rgba(34, 197, 94, 0.85)',
            'out_for_delivery' => 'rgba(59, 130, 246, 0.85)',
            'completed' => 'rgba(22, 163, 74, 0.85)',
            'cancelled' => 'rgba(239, 68, 68, 0.82)',
        ];

        return $palette[$status] ?? 'rgba(203, 213, 225, 0.95)';
    }

    protected function buildTopSellers($businessId)
    {
        return OrderItem::query()
            ->where('business_id', $businessId)
            ->whereHas('order', function ($query) use ($businessId) {
                $query->where('business_id', $businessId)->where('status', 'completed');
            })
            ->select(['product_id', 'product_name', DB::raw('SUM(quantity) as quantity_sold'), DB::raw('SUM(total_price) as sales')])
            ->groupBy(['product_id', 'product_name'])
            ->orderByDesc('quantity_sold')
            ->take(6)
            ->get();
    }

    protected function buildSlowMovingProducts($businessId)
    {
        return Product::query()
            ->where('business_id', $businessId)
            ->where('availability', true)
            ->select(['products.id', 'products.name'])
            ->leftJoin('order_items', function ($join) use ($businessId) {
                $join->on('products.id', '=', 'order_items.product_id')
                    ->where('order_items.business_id', $businessId);
            })
            ->leftJoin('orders', function ($join) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.status', 'completed');
            })
            ->groupBy(['products.id', 'products.name'])
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as quantity_sold')
            ->orderBy('quantity_sold', 'asc')
            ->take(6)
            ->get();
    }
}
