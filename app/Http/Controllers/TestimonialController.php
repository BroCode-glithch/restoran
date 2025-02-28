<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    //

    public function index()
    {
        toastr()->success('Page loaded successfully!', 'Congrats', ['timeOut' => 5000]);
        return view('testimonial');
    }
}
