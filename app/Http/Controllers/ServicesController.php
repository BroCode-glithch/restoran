<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServicesController extends Controller
{
    //
    public function index()
    {
        $businessId = currentBusinessId();

        return view('services', [
            'services' => Service::query()->where('business_id', $businessId)->latest()->get(),
        ]);
    }
}
