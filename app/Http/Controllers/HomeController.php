<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $businessId = currentBusinessId();
        $services = Service::where('business_id', $businessId)->orderBy('id', 'desc')->get();
        $teamMembers = User::query()
            ->where('business_id', $businessId)
            ->whereIn('role', ['manager', 'staff', 'kitchen_staff'])
            ->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();
        $customerStories = Order::query()
            ->where('business_id', $businessId)
            ->where('status', 'completed')
            ->latest()
            ->take(4)
            ->get();

        $breakfasts = Menu::where('business_id', $businessId)->where("category", "Breakfast")->where("status", "Active")->get();
        $lunches = Menu::where('business_id', $businessId)->where("category", "Lunch")->where("status", "Active")->get();
        $dinners = Menu::where('business_id', $businessId)->where("category", "Dinner")->where("status", "Active")->get();

        return view('home', compact('services', 'breakfasts', 'lunches', 'dinners', 'teamMembers', 'customerStories'));
    }
}
