<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    //
    public function logout(Request $request)
    {
        Auth::logout(); // Logs out user

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        toastr()->success('You logged out successfully!', 'See you soon', ['timeOut' => 5000]);
        return redirect("/")->with("success", "You have successfully logged out!");
    }
}
