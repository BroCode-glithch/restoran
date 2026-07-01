<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\FeatureFlag;
use App\Models\Order;
use App\Models\Product;
use App\Models\Settings;
use App\Models\SystemLog;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return redirect()->route(auth()->user()->dashboardRoute());
    }

    public function customer(CartService $cartService)
    {
        $businessId = currentBusinessId();
        $user = auth()->user();

        $orderQuery = Order::query()
            ->where('business_id', $businessId)
            ->where('user_id', $user->id);

        $orders = (clone $orderQuery)->latest()->take(5)->get();

        $featuredProducts = Product::query()
            ->where('business_id', $businessId)
            ->available()
            ->where('is_featured', true)
            ->with('category')
            ->latest()
            ->take(6)
            ->get();

        $totalOrders = (clone $orderQuery)->count();
        $completedOrders = (clone $orderQuery)->where('status', 'completed')->count();
        $averageOrderValue = (float) (clone $orderQuery)->avg('total');
        $orderTrend = $this->buildCountTrendSeries($orderQuery, 7);
        $statusMix = $this->buildStatusBreakdownSeries($orderQuery, [
            'placed',
            'confirmed',
            'preparing',
            'ready',
            'out_for_delivery',
            'completed',
            'cancelled',
        ]);

        return view('dashboards.overview', [
            'role' => 'customer',
            'title' => 'Customer Portal',
            'subtitle' => 'Browse the menu, build your cart and track every order in one place.',
            'stats' => [
                ['label' => 'Open Orders', 'value' => (clone $orderQuery)->whereNotIn('status', ['completed', 'cancelled'])->count(), 'note' => 'Active on your account', 'icon' => 'fa-solid fa-receipt'],
                ['label' => 'Completed', 'value' => (clone $orderQuery)->where('status', 'completed')->count(), 'note' => 'Delivered or picked up', 'icon' => 'fa-solid fa-circle-check'],
                ['label' => 'Cart Items', 'value' => $cartService->count(), 'note' => 'Ready to checkout', 'icon' => 'fa-solid fa-cart-shopping'],
                ['label' => 'Preferred Channel', 'value' => 'WhatsApp', 'note' => 'Quick updates enabled', 'icon' => 'fa-brands fa-whatsapp'],
            ],
            'insights' => [
                ['label' => 'Completion rate', 'value' => $totalOrders > 0 ? round(($completedOrders / max($totalOrders, 1)) * 100) . '%' : '0%', 'note' => 'Orders already delivered or collected'],
                ['label' => 'Average ticket', 'value' => moneyFormat($averageOrderValue), 'note' => 'Typical spend on your account'],
                ['label' => 'Latest activity', 'value' => $orders->isNotEmpty() ? optional($orders->first()->updated_at)->diffForHumans() : 'No recent orders', 'note' => 'Most recent order update'],
            ],
            'charts' => [
                $this->buildLineChart(
                    'customer-order-trend',
                    'Order trend',
                    'Your activity across the last 7 days',
                    $orderTrend['labels'],
                    $orderTrend['values'],
                    'Orders',
                    ['column_class' => 'col-lg-8']
                ),
                $this->buildDoughnutChart(
                    'customer-order-status',
                    'Status mix',
                    'Where your orders are in the pipeline',
                    $statusMix['labels'],
                    $statusMix['values'],
                    $statusMix['colors'],
                    ['column_class' => 'col-lg-4']
                ),
            ],
            'actions' => [
                ['label' => 'Browse Menu', 'route' => 'catalog.index', 'icon' => 'fa-solid fa-bowl-food', 'variant' => 'btn-warning'],
                ['label' => 'Open Cart', 'route' => 'cart.index', 'icon' => 'fa-solid fa-cart-shopping', 'variant' => 'btn-outline-light'],
                ['label' => 'View Orders', 'route' => 'orders.index', 'icon' => 'fa-solid fa-receipt', 'variant' => 'btn-outline-light'],
            ],
            'cards' => $featuredProducts->map(function ($product) {
                return [
                    'title' => $product->name,
                    'subtitle' => optional($product->category)->name ?: ucfirst($product->type),
                    'description' => $product->description,
                    'meta' => $product->type,
                    'price' => $product->price,
                    'image' => mediaUrl($product->image, asset('assets/img/menu-1.jpg')),
                    'action_route' => 'catalog.index',
                ];
            })->toArray(),
            'tables' => [
                [
                    'title' => 'Recent Orders',
                    'description' => 'Track your latest order activity and status changes.',
                    'columns' => ['Order', 'Status', 'Total', 'Updated'],
                    'rows' => $orders->map(function ($order) {
                return [
                    $order->order_number,
                    '<span class="badge ' . orderStatusBadge($order->status) . '">' . orderStatusLabel($order->status) . '</span>',
                    moneyFormat($order->total, $order->currency),
                    optional($order->updated_at)->diffForHumans(),
                ];
            })->toArray(),
                    'empty' => 'No orders yet. Start by browsing the menu.',
                    'action_route' => 'orders.index',
                    'action_label' => 'View All Orders',
                ],
            ],
        ]);
    }

    public function staff()
    {
        $orders = $this->baseOrdersQuery();
        $workQueue = (clone $orders)->where(function ($query) {
            $query->where('assigned_staff_id', auth()->id())->orWhereNull('assigned_staff_id');
        });
        $orderTrend = $this->buildCountTrendSeries($workQueue, 7);
        $statusMix = $this->buildStatusBreakdownSeries($workQueue, [
            'placed',
            'confirmed',
            'preparing',
            'ready',
            'out_for_delivery',
            'completed',
        ]);

        return $this->renderOperationalDashboard(
            'staff',
            'Staff Dashboard',
            'Follow assigned orders and update operational tasks quickly.',
            $this->staffStats(),
            $this->staffTables(),
            [],
            [
                $this->buildLineChart(
                    'staff-order-trend',
                    'Workload trend',
                    'Assigned and unassigned orders over the last 7 days',
                    $orderTrend['labels'],
                    $orderTrend['values'],
                    'Orders',
                    ['column_class' => 'col-lg-7']
                ),
                $this->buildDoughnutChart(
                    'staff-order-status',
                    'Queue mix',
                    'What the open queue looks like right now',
                    $statusMix['labels'],
                    $statusMix['values'],
                    $statusMix['colors'],
                    ['column_class' => 'col-lg-5']
                ),
            ],
            [
                ['label' => 'Assigned to you', 'value' => (clone $orders)->where('assigned_staff_id', auth()->id())->count(), 'note' => 'Orders you can move forward'],
                ['label' => 'Open queue', 'value' => (clone $orders)->whereNotIn('status', ['completed', 'cancelled'])->count(), 'note' => 'Requires action from the floor'],
                ['label' => 'Today', 'value' => (clone $orders)->whereDate('created_at', today())->count(), 'note' => 'Orders created today'],
            ],
            [
                ['label' => 'Open orders', 'route' => 'staff.orders.index', 'icon' => 'fa-solid fa-clipboard-list', 'variant' => 'btn-warning'],
                ['label' => 'Task list', 'route' => 'staff.tasks.index', 'icon' => 'fa-solid fa-list-check', 'variant' => 'btn-outline-light'],
            ]
        );
    }

    public function kitchen()
    {
        $orders = $this->baseOrdersQuery();
        $prepQueue = (clone $orders)->whereIn('status', ['placed', 'confirmed', 'preparing', 'ready']);
        $orderTrend = $this->buildCountTrendSeries($prepQueue, 7);
        $statusMix = $this->buildStatusBreakdownSeries($prepQueue, ['placed', 'confirmed', 'preparing', 'ready', 'out_for_delivery']);

        return $this->renderOperationalDashboard(
            'kitchen_staff',
            'Kitchen Dashboard',
            'Monitor the live queue and move orders through the prep pipeline.',
            $this->kitchenStats(),
            $this->kitchenTables(),
            [],
            [
                $this->buildLineChart(
                    'kitchen-order-trend',
                    'Prep trend',
                    'Incoming kitchen work over the last 7 days',
                    $orderTrend['labels'],
                    $orderTrend['values'],
                    'Orders',
                    ['column_class' => 'col-lg-7']
                ),
                $this->buildDoughnutChart(
                    'kitchen-order-status',
                    'Queue mix',
                    'Current stage distribution in the kitchen',
                    $statusMix['labels'],
                    $statusMix['values'],
                    $statusMix['colors'],
                    ['column_class' => 'col-lg-5']
                ),
            ],
            [
                ['label' => 'Queue', 'value' => (clone $orders)->whereIn('status', ['placed', 'confirmed'])->count(), 'note' => 'Orders waiting to be prepped'],
                ['label' => 'Preparing', 'value' => (clone $orders)->where('status', 'preparing')->count(), 'note' => 'Active prep tickets'],
                ['label' => 'Ready', 'value' => (clone $orders)->where('status', 'ready')->count(), 'note' => 'Awaiting handoff'],
            ],
            [
                ['label' => 'Open queue', 'route' => 'kitchen.orders.index', 'icon' => 'fa-solid fa-bell-concierge', 'variant' => 'btn-warning'],
                ['label' => 'Completed', 'route' => 'kitchen.orders.completed', 'icon' => 'fa-solid fa-circle-check', 'variant' => 'btn-outline-light'],
            ]
        );
    }

    public function manager()
    {
        $orders = $this->baseOrdersQuery();
        $completedOrders = (clone $orders)->where('status', 'completed');
        $revenueTrend = $this->buildSumTrendSeries($completedOrders, 'total', 7, 'updated_at');
        $statusMix = $this->buildStatusBreakdownSeries($orders, [
            'placed',
            'confirmed',
            'preparing',
            'ready',
            'out_for_delivery',
            'completed',
            'cancelled',
        ]);

        return $this->renderOperationalDashboard(
            'manager',
            'Manager Dashboard',
            'Keep an eye on sales, order flow, staff activity and menu visibility.',
            $this->managerStats(),
            $this->managerTables(),
            [],
            [
                $this->buildLineChart(
                    'manager-revenue-trend',
                    'Revenue trend',
                    'Completed orders across the last 7 days',
                    $revenueTrend['labels'],
                    $revenueTrend['values'],
                    'Revenue',
                    ['column_class' => 'col-lg-7', 'format' => 'currency']
                ),
                $this->buildDoughnutChart(
                    'manager-status-mix',
                    'Order lifecycle',
                    'How orders are moving through the business',
                    $statusMix['labels'],
                    $statusMix['values'],
                    $statusMix['colors'],
                    ['column_class' => 'col-lg-5']
                ),
            ],
            [
                ['label' => 'Revenue today', 'value' => moneyFormat((float) (clone $completedOrders)->whereDate('updated_at', today())->sum('total')), 'note' => 'Completed orders only'],
                ['label' => 'Open orders', 'value' => (clone $orders)->whereNotIn('status', ['completed', 'cancelled'])->count(), 'note' => 'Need manager attention'],
                ['label' => 'Active staff', 'value' => User::query()->where('business_id', currentBusinessId())->whereIn('role', ['staff', 'kitchen_staff'])->count(), 'note' => 'Shift coverage'],
            ],
            [
                ['label' => 'Orders', 'route' => 'manager.orders.index', 'icon' => 'fa-solid fa-clipboard-list', 'variant' => 'btn-warning'],
                ['label' => 'Reports', 'route' => 'manager.reports.index', 'icon' => 'fa-solid fa-chart-pie', 'variant' => 'btn-outline-light'],
                ['label' => 'Products', 'route' => 'admin.products.index', 'icon' => 'fa-solid fa-utensils', 'variant' => 'btn-outline-light'],
                ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'fa-solid fa-sliders', 'variant' => 'btn-outline-light'],
            ]
        );
    }

    public function admin()
    {
        $orders = $this->baseOrdersQuery();
        $completedOrders = (clone $orders)->where('status', 'completed');
        $revenueTrend = $this->buildSumTrendSeries($completedOrders, 'total', 7, 'updated_at');
        $statusMix = $this->buildStatusBreakdownSeries($orders, [
            'placed',
            'confirmed',
            'preparing',
            'ready',
            'out_for_delivery',
            'completed',
            'cancelled',
        ]);

        return $this->renderOperationalDashboard(
            'admin',
            'Admin Dashboard',
            'Manage the canteen workspace, monitor sales and keep operations aligned.',
            $this->managerStats(),
            $this->managerTables(),
            [],
            [
                $this->buildLineChart(
                    'admin-revenue-trend',
                    'Revenue trend',
                    'Completed orders across the last 7 days',
                    $revenueTrend['labels'],
                    $revenueTrend['values'],
                    'Revenue',
                    ['column_class' => 'col-lg-7', 'format' => 'currency']
                ),
                $this->buildDoughnutChart(
                    'admin-status-mix',
                    'Order lifecycle',
                    'How orders are moving through the business',
                    $statusMix['labels'],
                    $statusMix['values'],
                    $statusMix['colors'],
                    ['column_class' => 'col-lg-5']
                ),
            ],
            [
                ['label' => 'Revenue today', 'value' => moneyFormat((float) (clone $completedOrders)->whereDate('updated_at', today())->sum('total')), 'note' => 'Completed orders only'],
                ['label' => 'Open orders', 'value' => (clone $orders)->whereNotIn('status', ['completed', 'cancelled'])->count(), 'note' => 'Need attention'],
                ['label' => 'Active staff', 'value' => User::query()->where('business_id', currentBusinessId())->whereIn('role', ['staff', 'kitchen_staff'])->count(), 'note' => 'Shift coverage'],
            ],
            [
                ['label' => 'Orders', 'route' => 'manager.orders.index', 'icon' => 'fa-solid fa-clipboard-list', 'variant' => 'btn-warning'],
                ['label' => 'Reports', 'route' => 'manager.reports.index', 'icon' => 'fa-solid fa-chart-pie', 'variant' => 'btn-outline-light'],
                ['label' => 'Products', 'route' => 'admin.products.index', 'icon' => 'fa-solid fa-utensils', 'variant' => 'btn-outline-light'],
                ['label' => 'Inventory', 'route' => 'admin.inventory.index', 'icon' => 'fa-solid fa-boxes-stacked', 'variant' => 'btn-outline-light'],
            ]
        );
    }

    public function superAdmin()
    {
        $users = User::query()->where('business_id', currentBusinessId());
        $userTrend = $this->buildCountTrendSeries($users, 7);
        $roleMix = $this->buildRoleBreakdownSeries($users, [
            'customer',
            'staff',
            'kitchen_staff',
            'manager',
            'super_admin',
            'developer',
        ]);

        return $this->renderOperationalDashboard(
            'super_admin',
            'Super Admin Dashboard',
            'Control the business configuration, users and role assignments.',
            $this->superAdminStats(),
            $this->superAdminTables(),
            [],
            [
                $this->buildLineChart(
                    'super-admin-user-trend',
                    'User growth',
                    'Accounts created over the last 7 days',
                    $userTrend['labels'],
                    $userTrend['values'],
                    'Users',
                    ['column_class' => 'col-lg-7']
                ),
                $this->buildDoughnutChart(
                    'super-admin-role-mix',
                    'Role mix',
                    'How access is distributed across the workspace',
                    $roleMix['labels'],
                    $roleMix['values'],
                    $roleMix['colors'],
                    ['column_class' => 'col-lg-5']
                ),
            ],
            [
                ['label' => 'Active users', 'value' => User::query()->where('business_id', currentBusinessId())->where('is_active', true)->count(), 'note' => 'Enabled accounts'],
                ['label' => 'Inactive users', 'value' => User::query()->where('business_id', currentBusinessId())->where('is_active', false)->count(), 'note' => 'Accounts awaiting review'],
                ['label' => 'Role coverage', 'value' => User::query()->where('business_id', currentBusinessId())->distinct('role')->count('role'), 'note' => 'Unique role types'],
            ],
            [
                ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'fa-solid fa-users-gear', 'variant' => 'btn-warning'],
                ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'fa-solid fa-sliders', 'variant' => 'btn-outline-light'],
                ['label' => 'Flags', 'route' => 'admin.flags.index', 'icon' => 'fa-solid fa-flag', 'variant' => 'btn-outline-light'],
            ]
        );
    }

    public function developer()
    {
        $logs = SystemLog::query()->where('business_id', currentBusinessId());
        $logTrend = $this->buildCountTrendSeries($logs, 7);
        $levelMix = $this->buildLogLevelBreakdownSeries($logs);

        return $this->renderOperationalDashboard(
            'developer',
            'Developer Dashboard',
            'Inspect logs, feature flags and the full system configuration.',
            $this->developerStats(),
            $this->developerTables(),
            [],
            [
                $this->buildLineChart(
                    'developer-log-trend',
                    'Log trend',
                    'Captured events across the last 7 days',
                    $logTrend['labels'],
                    $logTrend['values'],
                    'Logs',
                    ['column_class' => 'col-lg-7']
                ),
                $this->buildDoughnutChart(
                    'developer-log-levels',
                    'Severity mix',
                    'Breakdown of the latest log entries',
                    $levelMix['labels'],
                    $levelMix['values'],
                    $levelMix['colors'],
                    ['column_class' => 'col-lg-5']
                ),
            ],
            [
                ['label' => 'System logs', 'value' => $logs->count(), 'note' => 'Captured events in the current business'],
                ['label' => 'Feature flags', 'value' => FeatureFlag::query()->where('business_id', currentBusinessId())->count(), 'note' => 'Runtime toggles available'],
                ['label' => 'Active flags', 'value' => FeatureFlag::query()->where('business_id', currentBusinessId())->where('enabled', true)->count(), 'note' => 'Enabled right now'],
            ],
            [
                ['label' => 'Logs', 'route' => 'developer.logs.index', 'icon' => 'fa-solid fa-memo-circle-info', 'variant' => 'btn-warning'],
                ['label' => 'Flags', 'route' => 'admin.flags.index', 'icon' => 'fa-solid fa-flag', 'variant' => 'btn-outline-light'],
                ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'fa-solid fa-sliders', 'variant' => 'btn-outline-light'],
            ]
        );
    }

    protected function renderOperationalDashboard($role, $title, $subtitle, array $stats, array $tables, array $cards = [], array $charts = [], array $insights = [], array $actions = [])
    {
        return view('dashboards.overview', [
            'role' => $role,
            'title' => $title,
            'subtitle' => $subtitle,
            'stats' => $stats,
            'tables' => $tables,
            'cards' => $cards,
            'charts' => $charts,
            'insights' => $insights,
            'actions' => $actions,
        ]);
    }

    protected function buildCountTrendSeries($query, $days = 7, $dateColumn = 'created_at')
    {
        $labels = [];
        $values = [];

        for ($offset = $days - 1; $offset >= 0; $offset--) {
            $date = Carbon::today()->subDays($offset);
            $labels[] = $date->format('D');
            $values[] = (clone $query)->whereDate($dateColumn, $date)->count();
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
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

    protected function buildStatusBreakdownSeries($query, array $statuses)
    {
        $labels = [];
        $values = [];
        $colors = [];

        foreach ($statuses as $status) {
            $labels[] = orderStatusLabel($status);
            $values[] = (clone $query)->where('status', $status)->count();
            $colors[] = $this->statusColor($status);
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'colors' => $colors,
        ];
    }

    protected function buildRoleBreakdownSeries($query, array $roles)
    {
        $labels = [];
        $values = [];
        $colors = [];

        foreach ($roles as $role) {
            $labels[] = roleLabel($role);
            $values[] = (clone $query)->where('role', $role)->count();
            $colors[] = $this->roleColor($role);
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'colors' => $colors,
        ];
    }

    protected function buildLogLevelBreakdownSeries($query)
    {
        $levels = ['error', 'warning', 'info', 'debug'];
        $labels = [];
        $values = [];
        $colors = [];

        foreach ($levels as $level) {
            $labels[] = strtoupper($level);
            $values[] = (clone $query)->where('level', $level)->count();
            $colors[] = $this->logColor($level);
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'colors' => $colors,
        ];
    }

    protected function buildLineChart($id, $title, $subtitle, array $labels, array $values, $seriesLabel = 'Trend', array $options = [])
    {
        return [
            'id' => $id,
            'title' => $title,
            'subtitle' => $subtitle,
            'type' => 'line',
            'column_class' => $options['column_class'] ?? 'col-lg-6',
            'height' => $options['height'] ?? 280,
            'legend' => $options['legend'] ?? false,
            'format' => $options['format'] ?? 'number',
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $seriesLabel,
                    'data' => $values,
                    'borderColor' => $options['borderColor'] ?? '#FEA116',
                    'backgroundColor' => $options['backgroundColor'] ?? 'rgba(254, 161, 22, 0.16)',
                    'pointBackgroundColor' => $options['pointBackgroundColor'] ?? '#FEA116',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                ],
            ],
        ];
    }

    protected function buildDoughnutChart($id, $title, $subtitle, array $labels, array $values, array $colors, array $options = [])
    {
        return [
            'id' => $id,
            'title' => $title,
            'subtitle' => $subtitle,
            'type' => 'doughnut',
            'column_class' => $options['column_class'] ?? 'col-lg-6',
            'height' => $options['height'] ?? 280,
            'legend' => $options['legend'] ?? true,
            'format' => $options['format'] ?? 'number',
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                ],
            ],
        ];
    }

    protected function statusColor($status)
    {
        $palette = [
            'placed' => 'rgba(100, 116, 139, 0.95)',
            'confirmed' => 'rgba(254, 161, 22, 0.88)',
            'preparing' => 'rgba(15, 23, 43, 0.86)',
            'ready' => 'rgba(254, 161, 22, 0.68)',
            'out_for_delivery' => 'rgba(51, 65, 85, 0.88)',
            'completed' => 'rgba(22, 163, 74, 0.85)',
            'cancelled' => 'rgba(239, 68, 68, 0.82)',
        ];

        return $palette[$status] ?? 'rgba(203, 213, 225, 0.95)';
    }

    protected function roleColor($role)
    {
        $palette = [
            'customer' => 'rgba(22, 163, 74, 0.85)',
            'staff' => 'rgba(14, 165, 233, 0.85)',
            'kitchen_staff' => 'rgba(245, 158, 11, 0.88)',
            'manager' => 'rgba(15, 23, 43, 0.86)',
            'super_admin' => 'rgba(254, 161, 22, 0.9)',
            'developer' => 'rgba(100, 116, 139, 0.9)',
        ];

        return $palette[$role] ?? 'rgba(203, 213, 225, 0.95)';
    }

    protected function logColor($level)
    {
        $palette = [
            'error' => 'rgba(239, 68, 68, 0.85)',
            'warning' => 'rgba(245, 158, 11, 0.88)',
            'info' => 'rgba(14, 165, 233, 0.85)',
            'debug' => 'rgba(100, 116, 139, 0.9)',
        ];

        return $palette[$level] ?? 'rgba(203, 213, 225, 0.95)';
    }

    protected function baseOrdersQuery()
    {
        return Order::query()->where('business_id', currentBusinessId());
    }

    protected function staffStats()
    {
        $orders = $this->baseOrdersQuery();

        return [
            ['label' => 'Open Orders', 'value' => (clone $orders)->whereNotIn('status', ['completed', 'cancelled'])->count(), 'note' => 'Pending attention', 'icon' => 'fa-solid fa-clipboard-list'],
            ['label' => 'Placed Today', 'value' => (clone $orders)->whereDate('created_at', today())->count(), 'note' => 'All incoming orders', 'icon' => 'fa-solid fa-bell-concierge'],
            ['label' => 'Ready', 'value' => (clone $orders)->where('status', 'ready')->count(), 'note' => 'Awaiting handoff', 'icon' => 'fa-solid fa-check'],
            ['label' => 'Assigned Tasks', 'value' => (clone $orders)->where('assigned_staff_id', auth()->id())->count(), 'note' => 'Assigned to you', 'icon' => 'fa-solid fa-user-check'],
        ];
    }

    protected function staffTables()
    {
        $orders = $this->baseOrdersQuery()
            ->where(function ($query) {
                $query->where('assigned_staff_id', auth()->id())->orWhereNull('assigned_staff_id');
            })
            ->latest()
            ->take(8)
            ->get();

        return [
            [
                'title' => 'Assigned / Open Orders',
                'description' => 'Work through your tasks and keep orders moving.',
                'columns' => ['Order', 'Customer', 'Status', 'Delivery', 'Total', 'Updated'],
                'rows' => $this->mapOrders($orders),
                'empty' => 'No assigned orders at the moment.',
                'action_route' => 'staff.orders.index',
                'action_label' => 'View Queue',
            ],
        ];
    }

    protected function kitchenStats()
    {
        $orders = $this->baseOrdersQuery();

        return [
            ['label' => 'Queue', 'value' => (clone $orders)->whereIn('status', ['placed', 'confirmed'])->count(), 'note' => 'Incoming orders', 'icon' => 'fa-solid fa-burger'],
            ['label' => 'Preparing', 'value' => (clone $orders)->where('status', 'preparing')->count(), 'note' => 'Active prep', 'icon' => 'fa-solid fa-fire-burner'],
            ['label' => 'Ready', 'value' => (clone $orders)->where('status', 'ready')->count(), 'note' => 'Ready for pickup', 'icon' => 'fa-solid fa-box-open'],
            ['label' => 'Completed Today', 'value' => (clone $orders)->where('status', 'completed')->whereDate('updated_at', today())->count(), 'note' => 'Closed tickets', 'icon' => 'fa-solid fa-circle-check'],
        ];
    }

    protected function kitchenTables()
    {
        $orders = $this->baseOrdersQuery()
            ->whereIn('status', ['placed', 'confirmed', 'preparing', 'ready'])
            ->latest()
            ->take(10)
            ->get();

        return [
            [
                'title' => 'Kitchen Queue',
                'description' => 'Live incoming orders and prep progress.',
                'columns' => ['Order', 'Customer', 'Status', 'Delivery', 'Total', 'Updated'],
                'rows' => $this->mapOrders($orders),
                'empty' => 'The queue is clear for now.',
                'action_route' => 'kitchen.orders.index',
                'action_label' => 'Open Queue',
            ],
        ];
    }

    protected function managerStats()
    {
        $orders = $this->baseOrdersQuery();
        $revenue = (clone $orders)->where('status', 'completed')->whereDate('updated_at', today())->sum('total');

        return [
            ['label' => 'Revenue Today', 'value' => moneyFormat($revenue), 'note' => 'Completed orders', 'icon' => 'fa-solid fa-naira-sign'],
            ['label' => 'Orders Today', 'value' => (clone $orders)->whereDate('created_at', today())->count(), 'note' => 'Current throughput', 'icon' => 'fa-solid fa-chart-line'],
            ['label' => 'Visible Products', 'value' => Product::query()->where('business_id', currentBusinessId())->where('availability', true)->count(), 'note' => 'Menu visibility', 'icon' => 'fa-solid fa-utensils'],
            ['label' => 'Active Staff', 'value' => User::query()->where('business_id', currentBusinessId())->whereIn('role', ['staff', 'kitchen_staff'])->count(), 'note' => 'Shift coverage', 'icon' => 'fa-solid fa-users'],
        ];
    }

    protected function managerTables()
    {
        return [
            [
                'title' => 'Order Monitoring',
                'description' => 'See the latest activity across the business.',
                'columns' => ['Order', 'Customer', 'Status', 'Delivery', 'Total', 'Updated'],
                'rows' => $this->mapOrders($this->baseOrdersQuery()->latest()->take(8)->get()),
                'empty' => 'No orders yet.',
                'action_route' => 'manager.orders.index',
                'action_label' => 'Open Order Monitor',
            ],
            [
                'title' => 'Feature Visibility',
                'description' => 'Quick snapshot of enabled operational flags.',
                'columns' => ['Flag', 'Status', 'Description'],
                'rows' => FeatureFlag::query()->where('business_id', currentBusinessId())->latest()->take(5)->get()->map(function ($flag) {
                    return [
                        $flag->label,
                        $flag->enabled ? '<span class="badge bg-success">Enabled</span>' : '<span class="badge bg-secondary">Disabled</span>',
                        $flag->description,
                    ];
                })->toArray(),
                'empty' => 'No feature flags configured.',
                'action_route' => 'admin.settings.index',
                'action_label' => 'Open Settings',
            ],
        ];
    }

    protected function superAdminStats()
    {
        return [
            ['label' => 'Users', 'value' => User::query()->where('business_id', currentBusinessId())->count(), 'note' => 'Account access', 'icon' => 'fa-solid fa-users-gear'],
            ['label' => 'Managers', 'value' => User::query()->where('business_id', currentBusinessId())->where('role', 'manager')->count(), 'note' => 'Business oversight', 'icon' => 'fa-solid fa-user-tie'],
            ['label' => 'Settings', 'value' => Settings::query()->where('business_id', currentBusinessId())->count(), 'note' => 'Configured values', 'icon' => 'fa-solid fa-sliders'],
            ['label' => 'Open Orders', 'value' => $this->baseOrdersQuery()->whereNotIn('status', ['completed', 'cancelled'])->count(), 'note' => 'Operations live', 'icon' => 'fa-solid fa-clipboard-list'],
        ];
    }

    protected function superAdminTables()
    {
        return [
            [
                'title' => 'Role Management',
                'description' => 'Review the active roles on the business account.',
                'columns' => ['Name', 'Email', 'Role', 'Status'],
                'rows' => User::query()->where('business_id', currentBusinessId())->latest()->take(8)->get()->map(function ($user) {
                    return [
                        $user->name,
                        $user->email,
                        '<span class="badge ' . roleBadgeClass($user->role) . '">' . roleLabel($user->role) . '</span>',
                        $user->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>',
                    ];
                })->toArray(),
                'empty' => 'No users available.',
                'action_route' => 'admin.users.index',
                'action_label' => 'Manage Users',
            ],
        ];
    }

    protected function developerStats()
    {
        return [
            ['label' => 'System Logs', 'value' => SystemLog::query()->where('business_id', currentBusinessId())->count(), 'note' => 'Captured events', 'icon' => 'fa-solid fa-bug'],
            ['label' => 'Feature Flags', 'value' => FeatureFlag::query()->where('business_id', currentBusinessId())->count(), 'note' => 'Runtime toggles', 'icon' => 'fa-solid fa-flag'],
            ['label' => 'Integrations', 'value' => 4, 'note' => 'Prepared for expansion', 'icon' => 'fa-solid fa-plug'],
            ['label' => 'Businesses', 'value' => Business::query()->count(), 'note' => 'Ready for multi-tenant', 'icon' => 'fa-solid fa-building'],
        ];
    }

    protected function developerTables()
    {
        $logs = SystemLog::query()->where('business_id', currentBusinessId())->latest()->paginate(10);

        return [
            [
                'title' => 'Recent Logs',
                'description' => 'Review events, order actions and configuration changes.',
                'columns' => ['Level', 'Category', 'Message', 'Time'],
                'rows' => $logs->getCollection()->map(function ($log) {
                    return [
                        '<span class="badge bg-dark">' . strtoupper($log->level) . '</span>',
                        ucfirst($log->category),
                        $log->message,
                        optional($log->created_at)->diffForHumans(),
                    ];
                })->toArray(),
                'pagination' => $logs,
                'empty' => 'No logs yet.',
                'action_route' => 'developer.logs.index',
                'action_label' => 'Open Logs',
            ],
            [
                'title' => 'Feature Flags',
                'description' => 'Check which toggles are active right now.',
                'columns' => ['Flag', 'Status', 'Description'],
                'rows' => FeatureFlag::query()->where('business_id', currentBusinessId())->latest()->take(5)->get()->map(function ($flag) {
                    return [
                        $flag->label,
                        $flag->enabled ? '<span class="badge bg-success">Enabled</span>' : '<span class="badge bg-secondary">Disabled</span>',
                        $flag->description,
                    ];
                })->toArray(),
                'empty' => 'No feature flags configured.',
                'action_route' => 'admin.flags.index',
                'action_label' => 'Manage Flags',
            ],
        ];
    }

    protected function mapOrders($orders)
    {
        return $orders->map(function ($order) {
                return [
                    $order->order_number,
                    $order->customer_name,
                    '<span class="badge ' . orderStatusBadge($order->status) . '">' . orderStatusLabel($order->status) . '</span>',
                    ucfirst($order->delivery_type),
                    moneyFormat($order->total, $order->currency),
                    optional($order->updated_at)->diffForHumans(),
                ];
            })->toArray();
    }
}
