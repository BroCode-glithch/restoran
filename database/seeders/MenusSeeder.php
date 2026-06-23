<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenusSeeder extends Seeder
{
    public function run()
    {
        $businessId = currentBusinessId();

        $menus = [
            ['name' => 'Jollof Rice Fiesta', 'category' => 'Breakfast', 'status' => 'Active', 'price' => 6500, 'description' => 'Smoky jollof rice served with grilled chicken, plantain and coleslaw.', 'image' => 'img/menu-1.jpg'],
            ['name' => 'Yam and Egg Combo', 'category' => 'Breakfast', 'status' => 'Active', 'price' => 3800, 'description' => 'Soft yam slices with a rich Nigerian egg sauce.', 'image' => 'img/menu-2.jpg'],
            ['name' => 'Pepper Soup Lunch Bowl', 'category' => 'Lunch', 'status' => 'Active', 'price' => 4800, 'description' => 'Light pepper soup with assorted meat and warm spice.', 'image' => 'img/menu-3.jpg'],
            ['name' => 'Crispy Chicken Wrap', 'category' => 'Lunch', 'status' => 'Active', 'price' => 5200, 'description' => 'Golden chicken wrap with fries and zesty sauce.', 'image' => 'img/menu-4.jpg'],
            ['name' => 'Ofada Rice Tray', 'category' => 'Dinner', 'status' => 'Active', 'price' => 7200, 'description' => 'Local ofada rice with ayamase sauce and boiled egg.', 'image' => 'img/menu-5.jpg'],
            ['name' => 'Catfish Pepper Soup', 'category' => 'Dinner', 'status' => 'Active', 'price' => 6100, 'description' => 'Spicy catfish pepper soup for a late evening meal.', 'image' => 'img/menu-6.jpg'],
            ['name' => 'Party Jollof Tray', 'category' => 'Dinner', 'status' => 'Active', 'price' => 32000, 'description' => 'Large tray ideal for events, teams and celebrations.', 'image' => 'img/menu-7.jpg'],
            ['name' => 'Zobo Citrus Blend', 'category' => 'Dinner', 'status' => 'Inactive', 'price' => 1500, 'description' => 'Chilled hibiscus drink with a citrus finish.', 'image' => 'img/menu-8.jpg'],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                [
                    'business_id' => $businessId,
                    'name' => $menu['name'],
                    'category' => $menu['category'],
                ],
                $menu + [
                    'business_id' => $businessId,
                ]
            );
        }
    }
}
