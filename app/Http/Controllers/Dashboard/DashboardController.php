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
        return $this->renderOperationalDashboard(
            'staff',
            'Staff Dashboard',
            'Follow assigned orders and update operational tasks quickly.',
            $this->staffStats(),
            $this->staffTables()
        );
    }

    public function kitchen()
    {
        return $this->renderOperationalDashboard(
            'kitchen_staff',
            'Kitchen Dashboard',
            'Monitor the live queue and move orders through the prep pipeline.',
            $this->kitchenStats(),
            $this->kitchenTables()
        );
    }

    public function manager()
    {
        return $this->renderOperationalDashboard(
            'manager',
            'Manager Dashboard',
            'Keep an eye on sales, order flow, staff activity and menu visibility.',
            $this->managerStats(),
            $this->managerTables()
        );
    }

    public function superAdmin()
    {
        return $this->renderOperationalDashboard(
            'super_admin',
            'Super Admin Dashboard',
            'Control the business configuration, users and role assignments.',
            $this->superAdminStats(),
            $this->superAdminTables()
        );
    }

    public function developer()
    {
        return $this->renderOperationalDashboard(
            'developer',
            'Developer Dashboard',
            'Inspect logs, feature flags and the full system configuration.',
            $this->developerStats(),
            $this->developerTables()
        );
    }

    protected function renderOperationalDashboard($role, $title, $subtitle, array $stats, array $tables)
    {
        return view('dashboards.overview', [
            'role' => $role,
            'title' => $title,
            'subtitle' => $subtitle,
            'stats' => $stats,
            'tables' => $tables,
        ]);
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
        return [
            [
                'title' => 'Recent Logs',
                'description' => 'Review events, order actions and configuration changes.',
                'columns' => ['Level', 'Category', 'Message', 'Time'],
                'rows' => SystemLog::query()->where('business_id', currentBusinessId())->latest()->take(10)->get()->map(function ($log) {
                    return [
                        '<span class="badge bg-dark">' . strtoupper($log->level) . '</span>',
                        ucfirst($log->category),
                        $log->message,
                        optional($log->created_at)->diffForHumans(),
                    ];
                })->toArray(),
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
