<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Menu;
use App\Models\Settings;
use App\Models\FeatureFlag;
use App\Models\User;
use App\Models\ProductCategory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            BusinessesSeeder::class,
            ServicesSeeder::class,
            MenusSeeder::class,
            ProductCategoriesSeeder::class,
            ProductsSeeder::class,
            FeatureFlagsSeeder::class,
            SettingsSeeder::class,
            UsersSeeder::class,
        ]);
    }
}
