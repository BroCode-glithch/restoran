<?php

namespace Database\Seeders;

use App\Models\Business;
use Illuminate\Database\Seeder;

class BusinessesSeeder extends Seeder
{
    public function run()
    {
        $business = Business::updateOrCreate(
            ['slug' => config('foodops.default_business.slug')],
            [
                'name' => config('foodops.default_business.name'),
                'status' => config('foodops.default_business.status'),
                'is_default' => true,
            ]
        );
    }
}
