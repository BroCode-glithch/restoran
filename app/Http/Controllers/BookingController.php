<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class BookingController extends Controller
{
    //

    public function index()
    {
        $businessId = currentBusinessId();

        return view('booking', [
            'services' => Service::query()->where('business_id', $businessId)->latest()->take(3)->get(),
            'whatsappNumber' => getSetting('contact.whatsapp_number', ''),
            'businessHours' => getSetting('operations.business_hours', ''),
            'deliveryFee' => getSetting('operations.delivery_fee', '0'),
        ]);
    }
}
