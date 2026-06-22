<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create users of all roles
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@bettyskitchen.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Chef User',
                'email' => 'chef@bettyskitchen.com',
                'password' => Hash::make('password'),
                'role' => 'chef',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
