<?php

use App\Services\BusinessContext;
use App\Services\RoleManager;
use App\Services\SettingsRepository;
use Illuminate\Support\Str;

if (!function_exists('currentBusiness')) {
    function currentBusiness()
    {
        return app(BusinessContext::class)->current();
    }
}

if (!function_exists('currentBusinessId')) {
    function currentBusinessId()
    {
        return app(BusinessContext::class)->currentId();
    }
}

if (!function_exists('getSetting')) {
    function getSetting($key, $default = null, $businessId = null)
    {
        return app(SettingsRepository::class)->get($key, $default, $businessId);
    }
}

if (!function_exists('setSetting')) {
    function setSetting($key, $value, $businessId = null, $section = null)
    {
        return app(SettingsRepository::class)->put($key, $value, $businessId, $section);
    }
}

if (!function_exists('dashboardNavigation')) {
    function dashboardNavigation($role)
    {
        return app(RoleManager::class)->navigationFor($role);
    }
}

if (!function_exists('customerBottomNavigation')) {
    function customerBottomNavigation()
    {
        return app(RoleManager::class)->customerBottomNavigation();
    }
}

if (!function_exists('roleDashboardRoute')) {
    function roleDashboardRoute($role)
    {
        return app(RoleManager::class)->dashboardRoute($role);
    }
}

if (!function_exists('roleLabel')) {
    function roleLabel($role)
    {
        return app(RoleManager::class)->label($role);
    }
}

if (!function_exists('roleBadgeClass')) {
    function roleBadgeClass($role)
    {
        return app(RoleManager::class)->badge($role);
    }
}

if (!function_exists('orderStatusLabel')) {
    function orderStatusLabel($status)
    {
        return app(RoleManager::class)->statusLabel($status);
    }
}

if (!function_exists('orderStatusBadge')) {
    function orderStatusBadge($status)
    {
        return app(RoleManager::class)->statusBadge($status);
    }
}

if (!function_exists('orderStatusNext')) {
    function orderStatusNext($status)
    {
        return app(RoleManager::class)->statusNext($status);
    }
}

if (!function_exists('dashboardBottomNavigation')) {
    function dashboardBottomNavigation($role)
    {
        return app(RoleManager::class)->bottomNavigationFor($role);
    }
}

if (!function_exists('moneyFormat')) {
    function moneyFormat($amount, $currency = null)
    {
        $currency = strtoupper(trim((string) ($currency ?: getSetting('operations.currency', 'NGN'))));

        return number_format((float) $amount, 2) . ' ' . $currency;
    }
}

// helper for getCurrencies from the foodops.php config file
if (!function_exists('getCurrencies')) {
    function getCurrencies()
    {
        return config('foodops.currency_symbols', []);
    }
}


if (!function_exists('mailIsConfigured')) {
    function mailIsConfigured()
    {
        $host = strtolower(trim((string) config('mail.mailers.smtp.host', '')));
        $username = trim((string) config('mail.mailers.smtp.username', ''));
        $password = trim((string) config('mail.mailers.smtp.password', ''));

        return $host !== '' && $host !== 'mailhog' && $username !== '' && $password !== '';
    }
}

if (!function_exists('mailIdentity')) {
    function mailIdentity($context = 'general')
    {
        $baseAddress = trim((string) config('mail.from.address', 'info@dailydewtech.com.ng'));
        $businessName = getSetting('branding.business_name', getSetting('site_title', config('app.name')));
        $domain = Str::after($baseAddress, '@');
        $context = strtolower(trim((string) $context));
        $prefixes = config('foodops.mail_sender_prefixes', []);
        $prefix = isset($prefixes[$context]) ? $prefixes[$context] : ($context !== '' ? Str::slug($context, '') : 'info');

        if ($prefix === '') {
            $prefix = 'info';
        }

        $address = filter_var($baseAddress, FILTER_VALIDATE_EMAIL) && !empty($domain)
            ? $prefix . '@' . $domain
            : $baseAddress;

        $label = $context !== '' ? ucfirst(str_replace(['_', '-'], ' ', $context)) . ' Notifications' : 'Notifications';

        return [
            'address' => $address,
            'name' => trim($label . ' | ' . $businessName),
        ];
    }
}

if (!function_exists('mediaUrl')) {
    function mediaUrl($path, $fallback = null)
    {
        if (empty($path)) {
            return $fallback ?: asset('assets/img/hero.png');
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        if (Str::startsWith($path, 'assets/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}

if (!function_exists('whatsappChatUrl')) {
    function whatsappChatUrl($phone, $message)
    {
        $phone = preg_replace('/[^0-9]/', '', (string) $phone);

        if (empty($phone)) {
            return null;
        }

        return 'https://wa.me/' . $phone . '?text=' . urlencode((string) $message);
    }
}
