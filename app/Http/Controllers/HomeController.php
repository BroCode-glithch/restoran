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
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $services = Service::all()->sortBy("desc");


        $breakfasts = Menu::where("category", "Breakfast")->where("status", "Active")->get();
        $lunches = Menu::where("category", "Lunch")->where("status", "Active")->get();
        $dinners = Menu::where("category", "Dinner")->where("status", "Active")->get();


        toastr()->success('Logged in successfully!', 'Welcome!', ['timeOut' => 5000]);
        return view('home', compact('services', 'breakfasts', 'lunches', 'dinners'));
    }
}
