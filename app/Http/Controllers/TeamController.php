<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class TeamController extends Controller
{
    //

    public function index()
    {
        $businessId = currentBusinessId();

        return view('team', [
            'teamMembers' => User::query()
                ->where('business_id', $businessId)
                ->whereIn('role', ['manager', 'staff', 'kitchen_staff'])
                ->where('is_active', true)
                ->latest()
                ->get(),
        ]);
    }
}
