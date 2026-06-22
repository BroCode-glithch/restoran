<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Menu;

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

        $breakfasts = Menu::where('business_id', $businessId)->where("category", "Breakfast")->where("status", "Active")->get();
        $lunches = Menu::where('business_id', $businessId)->where("category", "Lunch")->where("status", "Active")->get();
        $dinners = Menu::where('business_id', $businessId)->where("category", "Dinner")->where("status", "Active")->get();

        return view('home', compact('services', 'breakfasts', 'lunches', 'dinners'));
    }
}
