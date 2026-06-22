<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Settings;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $businessId = currentBusinessId();

        foreach (config('foodops.default_settings', []) as $key => $value) {
            Settings::updateOrCreate(
                [
                    'business_id' => $businessId,
                    'key' => $key,
                ],
                [
                    'section' => $this->sectionForKey($key),
                    'value' => $value,
                    'is_public' => true,
                ]
            );
        }
    }

    protected function sectionForKey($key)
    {
        if (strpos($key, 'branding.') === 0) {
            return 'branding';
        }

        if (strpos($key, 'contact.') === 0) {
            return 'contact';
        }

        if (strpos($key, 'operations.') === 0) {
            return 'operations';
        }

        if (strpos($key, 'notifications.') === 0) {
            return 'notifications';
        }

        if (strpos($key, 'integrations.') === 0) {
            return 'integrations';
        }

        return 'general';
    }
}
