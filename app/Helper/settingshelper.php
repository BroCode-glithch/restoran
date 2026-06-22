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
