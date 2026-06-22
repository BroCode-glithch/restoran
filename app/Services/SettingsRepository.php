<?php

namespace App\Services;

use App\Models\Settings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SettingsRepository
{
    public function all($businessId = null)
    {
        try {
            if (!Schema::hasTable('settings')) {
                return [];
            }
        } catch (\Throwable $e) {
            return [];
        }

        $businessId = $businessId ?? app(BusinessContext::class)->currentId();
        $cacheKey = $this->cacheKey($businessId);

        return Cache::rememberForever($cacheKey, function () use ($businessId) {
            $current = Settings::query()
                ->where('business_id', $businessId)
                ->pluck('value', 'key')
                ->toArray();

            $global = Settings::query()
                ->whereNull('business_id')
                ->pluck('value', 'key')
                ->toArray();

            return array_merge($global, $current);
        });
    }

    public function get($key, $default = null, $businessId = null)
    {
        $settings = $this->all($businessId);

        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }

    public function put($key, $value, $businessId = null, $section = null)
    {
        try {
            if (!Schema::hasTable('settings')) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        $businessId = $businessId ?? app(BusinessContext::class)->currentId();

        Settings::query()->updateOrCreate(
            [
                'business_id' => $businessId,
                'key' => $key,
            ],
            [
                'section' => $section,
                'value' => $this->normalizeValue($value),
            ]
        );

        $this->flush($businessId);
    }

    public function putMany(array $settings, $businessId = null, $section = null)
    {
        foreach ($settings as $key => $value) {
            $this->put($key, $value, $businessId, $section);
        }
    }

    public function flush($businessId = null)
    {
        $businessId = $businessId ?? app(BusinessContext::class)->currentId();

        Cache::forget($this->cacheKey($businessId));
        Cache::forget($this->cacheKey(null));
    }

    protected function cacheKey($businessId)
    {
        return 'foodops.settings.' . ($businessId ?: 'global');
    }

    protected function normalizeValue($value)
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if ($value === null) {
            return '';
        }

        return (string) $value;
    }
}
