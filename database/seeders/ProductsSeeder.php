<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $businessId = currentBusinessId();

        $mealCategory = ProductCategory::query()->where('business_id', $businessId)->where('slug', 'signature-meals')->first();
        $drinkCategory = ProductCategory::query()->where('business_id', $businessId)->where('slug', 'drinks')->first();
        $cateringCategory = ProductCategory::query()->where('business_id', $businessId)->where('slug', 'catering-packages')->first();

        $products = [
            ['category_id' => optional($mealCategory)->id, 'name' => 'Jollof Rice Supreme', 'slug' => 'jollof-rice-supreme', 'description' => 'Smoky jollof rice served with tender chicken and plantain.', 'price' => 6500, 'image' => 'assets/img/menu-1.jpg', 'availability' => true, 'type' => 'meal', 'is_featured' => true],
            ['category_id' => optional($mealCategory)->id, 'name' => 'Pepper Soup Combo', 'slug' => 'pepper-soup-combo', 'description' => 'Light and spicy soup with assorted meat.', 'price' => 4800, 'image' => 'assets/img/menu-2.jpg', 'availability' => true, 'type' => 'meal', 'is_featured' => true],
            ['category_id' => optional($mealCategory)->id, 'name' => 'Ofada Rice Bowl', 'slug' => 'ofada-rice-bowl', 'description' => 'Local rice with ayamase sauce and boiled egg.', 'price' => 7200, 'image' => 'assets/img/menu-3.jpg', 'availability' => true, 'type' => 'meal', 'is_featured' => true],
            ['category_id' => optional($mealCategory)->id, 'name' => 'Crispy Chicken Wrap', 'slug' => 'crispy-chicken-wrap', 'description' => 'Handheld chicken wrap with fries and creamy sauce.', 'price' => 5200, 'image' => 'assets/img/menu-4.jpg', 'availability' => true, 'type' => 'meal', 'is_featured' => false],
            ['category_id' => optional($drinkCategory)->id, 'name' => 'Zobo Citrus Blend', 'slug' => 'zobo-citrus-blend', 'description' => 'Chilled hibiscus drink with citrus finish.', 'price' => 1500, 'image' => 'assets/img/menu-5.jpg', 'availability' => true, 'type' => 'drink', 'is_featured' => false],
            ['category_id' => optional($drinkCategory)->id, 'name' => 'Malt Breeze', 'slug' => 'malt-breeze', 'description' => 'Smooth malt drink served ice-cold.', 'price' => 1200, 'image' => 'assets/img/menu-6.jpg', 'availability' => true, 'type' => 'drink', 'is_featured' => false],
            ['category_id' => optional($cateringCategory)->id, 'name' => 'Event Tray Package', 'slug' => 'event-tray-package', 'description' => 'Large catering tray for corporate and private events.', 'price' => 45000, 'image' => 'assets/img/menu-7.jpg', 'availability' => true, 'type' => 'catering', 'is_featured' => true],
            ['category_id' => optional($cateringCategory)->id, 'name' => 'Office Lunch Pack', 'slug' => 'office-lunch-pack', 'description' => 'Pre-packed lunch bundles for team lunches and meetings.', 'price' => 18500, 'image' => 'assets/img/menu-8.jpg', 'availability' => true, 'type' => 'catering', 'is_featured' => false],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                [
                    'business_id' => $businessId,
                    'slug' => $product['slug'],
                ],
                $product + [
                    'business_id' => $businessId,
                ]
            );
        }
    }
}
