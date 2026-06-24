<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;

class AboutController extends Controller
{
    //
    public function index()
    {
        $businessId = currentBusinessId();

        return view('about', [
            'services' => Service::query()->where('business_id', $businessId)->latest()->take(4)->get(),
            'menuCount' => Menu::query()->where('business_id', $businessId)->count(),
            'serviceCount' => Service::query()->where('business_id', $businessId)->count(),
            'teamCount' => User::query()->where('business_id', $businessId)->whereIn('role', ['manager', 'staff', 'kitchen_staff'])->where('is_active', true)->count(),
            'completedOrders' => Order::query()->where('business_id', $businessId)->where('status', 'completed')->count(),
        ]);
    }
}
