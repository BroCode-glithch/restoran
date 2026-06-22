<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $businessId = currentBusinessId();

        $services = [
            [
                'icon' => 'fa fa-3x fa-user-tie',
                'title' => 'Master Chefs',
                'description' => 'Diam elitr kasd sed at elitr sed ipsum justo dolor sed clita amet diam',
            ],
            [
                'icon' => 'fa fa-3x fa-utensils',
                'title' => 'Quality Food',
                'description' => 'Diam elitr kasd sed at elitr sed ipsum justo dolor sed clita amet diam',
            ],
            [
                'icon' => 'fa fa-3x fa-cart-plus',
                'title' => 'Online Order',
                'description' => 'Diam elitr kasd sed at elitr sed ipsum justo dolor sed clita amet diam',
            ],
            [
                'icon' => 'fa fa-3x fa-headset',
                'title' => '24/7 Service',
                'description' => 'Diam elitr kasd sed at elitr sed ipsum justo dolor sed clita amet diam',
            ],
        ];

        foreach ($services as $service) {
            $service['business_id'] = $businessId;
            Service::create($service);
        }
    }
}
