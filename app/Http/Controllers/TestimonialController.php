<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class TestimonialController extends Controller
{
    //

    public function index()
    {
        $businessId = currentBusinessId();

        return view('testimonial', [
            'customerStories' => Order::query()
                ->where('business_id', $businessId)
                ->where('status', 'completed')
                ->latest()
                ->take(6)
                ->get(),
        ]);
    }
}
