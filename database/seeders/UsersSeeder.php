<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $businessId = currentBusinessId();

        $users = [
            [
                'name' => 'Ariyomi Miracle',
                'email' => 'ariyomiracle1234@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'developer',
                'phone' => '+2348010000001',
            ],
            [
                'name' => 'Emma Ariyomi',
                'email' => 'emmaariyom1@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'phone' => '+2348010000002',
            ],
            [
                'name' => 'Business Manager',
                'email' => 'manager@bettyskitchen.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'phone' => '+2348010000003',
            ],
            [
                'name' => 'Kitchen Lead',
                'email' => 'kitchen@bettyskitchen.com',
                'password' => Hash::make('password'),
                'role' => 'kitchen_staff',
                'phone' => '+2348010000004',
            ],
            [
                'name' => 'Front Desk Staff',
                'email' => 'staff@bettyskitchen.com',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '+2348010000005',
            ],
            [
                'name' => 'Demo Customer',
                'email' => 'customer@bettyskitchen.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '+2348010000006',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user + [
                    'business_id' => $businessId,
                    'is_active' => true,
                ]
            );
        }
    }
}
