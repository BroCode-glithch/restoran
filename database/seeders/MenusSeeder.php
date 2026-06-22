<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $businessId = currentBusinessId();

        $menus = [
            // Breakfast
            [
                'name' => 'Chicken Burger',
                'category' => 'Breakfast',
                'status' => 'Active',
                'price' => 115,
                'description' => 'Ipsum ipsum clita erat amet dolor justo diam',
                'image' => 'img/menu-1.jpg',
            ],
            [
                'name' => 'Chicken Burger',
                'category' => 'Breakfast',
                'status' => 'Active',
                'price' => 115,
                'description' => 'Ipsum ipsum clita erat amet dolor justo diam',
                'image' => 'img/menu-2.jpg',
            ],
            // Lunch
            [
                'name' => 'Chicken Burger',
                'category' => 'Lunch',
                'status' => 'Active',
                'price' => 115,
                'description' => 'Ipsum ipsum clita erat amet dolor justo diam',
                'image' => 'img/menu-3.jpg',
            ],
            [
                'name' => 'Chicken Burger',
                'category' => 'Lunch',
                'status' => 'Active',
                'price' => 115,
                'description' => 'Ipsum ipsum clita erat amet dolor justo diam',
                'image' => 'img/menu-4.jpg',
            ],
            // Dinner
            [
                'name' => 'Chicken Burger',
                'category' => 'Dinner',
                'status' => 'Active',
                'price' => 115,
                'description' => 'Ipsum ipsum clita erat amet dolor justo diam',
                'image' => 'img/menu-5.jpg',
            ],
            [
                'name' => 'Chicken Burger',
                'category' => 'Dinner',
                'status' => 'Active',
                'price' => 115,
                'description' => 'Ipsum ipsum clita erat amet dolor justo diam',
                'image' => 'img/menu-6.jpg',
            ],
        ];

        foreach ($menus as $menu) {
            $menu['business_id'] = $businessId;
            Menu::create($menu);
        }
    }
}
