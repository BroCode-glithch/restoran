<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagsSeeder extends Seeder
{
    public function run()
    {
        $flags = [
            [
                'key' => 'customer_mobile_bottom_nav',
                'label' => 'Customer mobile bottom navigation',
                'description' => 'Show the customer quick navigation on mobile screens.',
                'enabled' => true,
            ],
            [
                'key' => 'whatsapp_notifications',
                'label' => 'WhatsApp notifications',
                'description' => 'Enable WhatsApp quick-chat notifications for orders.',
                'enabled' => true,
            ],
            [
                'key' => 'email_notifications',
                'label' => 'Email notifications',
                'description' => 'Enable email notifications for orders and account actions.',
                'enabled' => true,
            ],
            [
                'key' => 'multi_business_ready',
                'label' => 'Multi-business ready',
                'description' => 'Prepare the platform for future SaaS tenant expansion.',
                'enabled' => true,
            ],
        ];

        foreach ($flags as $flag) {
            FeatureFlag::updateOrCreate(
                [
                    'business_id' => currentBusinessId(),
                    'key' => $flag['key'],
                ],
                $flag + [
                    'business_id' => currentBusinessId(),
                ]
            );
        }
    }
}
