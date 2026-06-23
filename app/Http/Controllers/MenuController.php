<?php

namespace App\Http\Controllers;

use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        $businessId = currentBusinessId();

        $breakfasts = Menu::where(
            'business_id', $businessId)
            ->where('category', 'Breakfast')
            ->where('status', 'Active')->get();
        $lunches = Menu::where(
            'business_id', $businessId)
            ->where('category', 'Lunch')
            ->where('status', 'Active')->get();
        $dinners = Menu::where(
            'business_id', $businessId)
            ->where('category', 'Dinner')
            ->where('status', 'Active')->get();

        return view('menu', compact(
            'breakfasts',
            'lunches',
            'dinners'
        ));
    }
}
