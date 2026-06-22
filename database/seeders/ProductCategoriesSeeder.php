<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Signature Meals',
                'slug' => 'signature-meals',
                'description' => 'House favorites and best sellers.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Drinks',
                'slug' => 'drinks',
                'description' => 'Fresh drinks, juices and beverages.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Catering Packages',
                'slug' => 'catering-packages',
                'description' => 'Large-format catering trays and event menus.',
                'sort_order' => 3,
            ],
        ];

        foreach ($categories as $category) {
            ProductCategory::updateOrCreate(
                [
                    'business_id' => currentBusinessId(),
                    'slug' => $category['slug'],
                ],
                $category + [
                    'business_id' => currentBusinessId(),
                    'is_visible' => true,
                ]
            );
        }
    }
}
