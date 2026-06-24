<?php

return [
    'roles' => [
        'developer' => [
            'level' => 60,
            'label' => 'Developer',
            'dashboard_route' => 'developer.dashboard',
            'badge' => 'bg-dark text-white',
        ],
        'super_admin' => [
            'level' => 50,
            'label' => 'Super Admin',
            'dashboard_route' => 'super-admin.dashboard',
            'badge' => 'bg-danger text-white',
        ],
        'manager' => [
            'level' => 40,
            'label' => 'Manager',
            'dashboard_route' => 'manager.dashboard',
            'badge' => 'bg-primary text-white',
        ],
        'kitchen_staff' => [
            'level' => 30,
            'label' => 'Kitchen Staff',
            'dashboard_route' => 'kitchen.dashboard',
            'badge' => 'bg-warning text-dark',
        ],
        'staff' => [
            'level' => 20,
            'label' => 'Staff',
            'dashboard_route' => 'staff.dashboard',
            'badge' => 'bg-info text-dark',
        ],
        'customer' => [
            'level' => 10,
            'label' => 'Customer',
            'dashboard_route' => 'customer.dashboard',
            'badge' => 'bg-success text-white',
        ],
    ],

    'auto_promote_emails' => [
        'developer' => [
            'ariyomiracle1234@gmail.com',
        ],
        'super_admin' => [
            'emmaariyom1@gmail.com',
        ],
    ],

    'order_status_pipeline' => [
        'placed' => [
            'label' => 'Placed',
            'next' => 'confirmed',
        ],
        'confirmed' => [
            'label' => 'Confirmed',
            'next' => 'preparing',
        ],
        'preparing' => [
            'label' => 'Preparing',
            'next' => 'ready',
        ],
        'ready' => [
            'label' => 'Ready',
            'next' => 'out_for_delivery',
        ],
        'out_for_delivery' => [
            'label' => 'Out for Delivery',
            'next' => 'completed',
        ],
        'completed' => [
            'label' => 'Completed',
            'next' => null,
        ],
        'cancelled' => [
            'label' => 'Cancelled',
            'next' => null,
        ],
    ],

    'status_badges' => [
        'placed' => 'bg-secondary text-white',
        'confirmed' => 'bg-primary text-white',
        'preparing' => 'bg-warning text-dark',
        'ready' => 'bg-info text-dark',
        'out_for_delivery' => 'bg-dark text-white',
        'completed' => 'bg-success text-white',
        'cancelled' => 'bg-danger text-white',
    ],

    'mail_sender_prefixes' => [
        'general' => 'info',
        'notification' => 'notify',
        'order' => 'order',
        'orders' => 'order',
        'account' => 'account',
        'support' => 'support',
        'system' => 'system',
    ],

    'dashboard_navigation' => [
        'customer' => [
            ['label' => 'Overview', 'route' => 'customer.dashboard', 'icon' => 'fa-solid fa-gauge'],
            ['label' => 'Menu', 'route' => 'catalog.index', 'icon' => 'fa-solid fa-bowl-food'],
            ['label' => 'Cart', 'route' => 'cart.index', 'icon' => 'fa-solid fa-cart-shopping'],
            ['label' => 'Orders', 'route' => 'orders.index', 'icon' => 'fa-solid fa-receipt'],
        ],
        'staff' => [
            ['label' => 'Dashboard', 'route' => 'staff.dashboard', 'icon' => 'fa-solid fa-gauge-high'],
            ['label' => 'Orders', 'route' => 'staff.orders.index', 'icon' => 'fa-solid fa-clipboard-list'],
            ['label' => 'Tasks', 'route' => 'staff.tasks.index', 'icon' => 'fa-solid fa-list-check'],
        ],
        'kitchen_staff' => [
            ['label' => 'Dashboard', 'route' => 'kitchen.dashboard', 'icon' => 'fa-solid fa-fire-burner'],
            ['label' => 'Queue', 'route' => 'kitchen.orders.index', 'icon' => 'fa-solid fa-bell-concierge'],
            ['label' => 'Completed', 'route' => 'kitchen.orders.completed', 'icon' => 'fa-solid fa-circle-check'],
        ],
        'manager' => [
            ['label' => 'Dashboard', 'route' => 'manager.dashboard', 'icon' => 'fa-solid fa-chart-column'],
            ['label' => 'Orders', 'route' => 'manager.orders.index', 'icon' => 'fa-solid fa-clipboard-list'],
            ['label' => 'Services', 'route' => 'admin.services.index', 'icon' => 'fa-solid fa-bell-concierge'],
            ['label' => 'Products', 'route' => 'admin.products.index', 'icon' => 'fa-solid fa-utensils'],
            ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'fa-solid fa-sliders'],
        ],
        'super_admin' => [
            ['label' => 'Dashboard', 'route' => 'super-admin.dashboard', 'icon' => 'fa-solid fa-shield-halved'],
            ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'fa-solid fa-users-gear'],
            ['label' => 'Services', 'route' => 'admin.services.index', 'icon' => 'fa-solid fa-bell-concierge'],
            ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'fa-solid fa-sliders'],
            ['label' => 'Flags', 'route' => 'admin.flags.index', 'icon' => 'fa-solid fa-flag'],
        ],
        'developer' => [
            ['label' => 'Dashboard', 'route' => 'developer.dashboard', 'icon' => 'fa-solid fa-code'],
            ['label' => 'Logs', 'route' => 'developer.logs.index', 'icon' => 'fa-solid fa-memo-circle-info'],
            ['label' => 'Services', 'route' => 'admin.services.index', 'icon' => 'fa-solid fa-bell-concierge'],
            ['label' => 'Flags', 'route' => 'admin.flags.index', 'icon' => 'fa-solid fa-flag'],
            ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'fa-solid fa-sliders'],
        ],
    ],

    'dashboard_bottom_navigation' => [
        'customer' => [
            ['label' => 'Home', 'route' => 'customer.dashboard', 'icon' => 'fa-solid fa-house'],
            ['label' => 'Menu', 'route' => 'catalog.index', 'icon' => 'fa-solid fa-bowl-food'],
            ['label' => 'Cart', 'route' => 'cart.index', 'icon' => 'fa-solid fa-cart-shopping'],
            ['label' => 'Orders', 'route' => 'orders.index', 'icon' => 'fa-solid fa-receipt'],
            ['label' => 'Account', 'route' => 'dashboard', 'icon' => 'fa-solid fa-user'],
        ],
        'staff' => [
            ['label' => 'Home', 'route' => 'staff.dashboard', 'icon' => 'fa-solid fa-house'],
            ['label' => 'Orders', 'route' => 'staff.orders.index', 'icon' => 'fa-solid fa-clipboard-list'],
            ['label' => 'Tasks', 'route' => 'staff.tasks.index', 'icon' => 'fa-solid fa-list-check'],
            ['label' => 'Account', 'route' => 'dashboard', 'icon' => 'fa-solid fa-user'],
        ],
        'kitchen_staff' => [
            ['label' => 'Home', 'route' => 'kitchen.dashboard', 'icon' => 'fa-solid fa-house'],
            ['label' => 'Queue', 'route' => 'kitchen.orders.index', 'icon' => 'fa-solid fa-bell-concierge'],
            ['label' => 'Done', 'route' => 'kitchen.orders.completed', 'icon' => 'fa-solid fa-circle-check'],
            ['label' => 'Account', 'route' => 'dashboard', 'icon' => 'fa-solid fa-user'],
        ],
        'manager' => [
            ['label' => 'Home', 'route' => 'manager.dashboard', 'icon' => 'fa-solid fa-house'],
            ['label' => 'Orders', 'route' => 'manager.orders.index', 'icon' => 'fa-solid fa-clipboard-list'],
            ['label' => 'Products', 'route' => 'admin.products.index', 'icon' => 'fa-solid fa-utensils'],
            ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'fa-solid fa-sliders'],
        ],
        'super_admin' => [
            ['label' => 'Home', 'route' => 'super-admin.dashboard', 'icon' => 'fa-solid fa-house'],
            ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'fa-solid fa-users-gear'],
            ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'fa-solid fa-sliders'],
            ['label' => 'Flags', 'route' => 'admin.flags.index', 'icon' => 'fa-solid fa-flag'],
        ],
        'developer' => [
            ['label' => 'Home', 'route' => 'developer.dashboard', 'icon' => 'fa-solid fa-house'],
            ['label' => 'Logs', 'route' => 'developer.logs.index', 'icon' => 'fa-solid fa-memo-circle-info'],
            ['label' => 'Flags', 'route' => 'admin.flags.index', 'icon' => 'fa-solid fa-flag'],
            ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'fa-solid fa-sliders'],
        ],
    ],

    'default_business' => [
        'name' => "Betty's Kitchen",
        'slug' => 'bettys-kitchen',
        'status' => 'active',
        'is_default' => true,
    ],

    'default_settings' => [
        'site_title' => "Betty's Kitchen",
        'site_icon' => 'fa fa-utensils me-3',
        'site_description' => "Betty's Kitchen is a modern food ordering and catering platform built to serve guests, teams and events with speed and consistency.",
        'branding.business_name' => "Betty's Kitchen",
        'branding.primary_color' => '#FEA116',
        'branding.secondary_color' => '#0F172B',
        'branding.font_family' => '"Nunito", sans-serif',
        'branding.logo_url' => '',
        'branding.favicon_url' => '',
        'contact.email' => 'contact@bettyskitchen.com',
        'contact.phone' => '+1 234 567 890',
        'contact.whatsapp_number' => '+2348012345678',
        'contact.address' => '123 Main Street, Anytown, USA',
        'operations.currency' => 'NGN',
        'operations.delivery_fee' => '0',
        'operations.pickup_enabled' => '1',
        'operations.delivery_enabled' => '1',
        'operations.business_hours' => 'Mon-Sat 09:00-21:00 | Sun 10:00-20:00',
        'notifications.whatsapp_enabled' => '1',
        'notifications.whatsapp_template' => 'Hello {customer_name}, your order {order_number} is now {status}.',
        'notifications.email_enabled' => '1',
        'integrations.stripe_enabled' => '0',
        'integrations.paystack_enabled' => '0',
        'integrations.resend_enabled' => '0',
        'integrations.twilio_enabled' => '0',
    ],

    'currency_symbols' => [

        // Major global currencies
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'JPY' => '¥',
        'CNY' => '¥',
        'INR' => '₹',
        'CHF' => 'CHF',
        'CAD' => '$',
        'AUD' => '$',
        'NZD' => '$',

        // Africa
        'NGN' => '₦',
        'GHS' => '₵',
        'KES' => 'KSh',
        'UGX' => 'USh',
        'TZS' => 'TSh',
        'ZAR' => 'R',
        'EGP' => 'E£',
        'MAD' => 'DH',
        'ETB' => 'Br',
        'XOF' => 'CFA',
        'XAF' => 'FCFA',
        'DZD' => 'DA',
        'TND' => 'DT',
        'AOA' => 'Kz',
        'ZMW' => 'ZK',
        'BWP' => 'P',
        'MUR' => '₨',
        'RWF' => 'FRw',

        // Europe
        'SEK' => 'kr',
        'NOK' => 'kr',
        'DKK' => 'kr',
        'PLN' => 'zł',
        'CZK' => 'Kč',
        'HUF' => 'Ft',
        'RON' => 'lei',
        'BGN' => 'лв',
        'TRY' => '₺',
        'RUB' => '₽',
        'UAH' => '₴',

        // Middle East
        'SAR' => '﷼',
        'AED' => 'د.إ',
        'QAR' => 'ر.ق',
        'KWD' => 'د.ك',
        'BHD' => '.د.ب',
        'OMR' => '﷼',
        'ILS' => '₪',
        'JOD' => 'د.ا',
        'LBP' => '£',

        // Asia
        'KRW' => '₩',
        'THB' => '฿',
        'VND' => '₫',
        'MYR' => 'RM',
        'IDR' => 'Rp',
        'PHP' => '₱',
        'PKR' => '₨',
        'BDT' => '৳',
        'LKR' => '₨',
        'NPR' => '₨',
        'MMK' => 'K',
        'KHR' => '៛',

        // Americas
        'MXN' => '$',
        'BRL' => 'R$',
        'ARS' => '$',
        'CLP' => '$',
        'COP' => '$',
        'PEN' => 'S/',
        'UYU' => '$U',
        'BOB' => 'Bs.',
        'PYG' => '₲',
        'DOP' => 'RD$',
        'GTQ' => 'Q',
        'CRC' => '₡',
        'HNL' => 'L',
        'NIO' => 'C$',
        'PAB' => 'B/.',

        // Others / crypto-style fiat edge cases
        'HKD' => '$',
        'SGD' => '$',
        'TWD' => 'NT$',
        'ISK' => 'kr',

    ],
];
