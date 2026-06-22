<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use App\Providers\RouteServiceProvider;
use App\Services\RoleManager;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Display toast after successful login.
     *
     * @return string
     */
    protected function authenticated(Request $request, $user)
    {
        session(['business_id' => $user->business_id]);
        $user->forceFill(['last_login_at' => now()])->save();

        SystemLog::create([
            'business_id' => $user->business_id,
            'actor_user_id' => $user->id,
            'level' => 'info',
            'category' => 'auth',
            'message' => 'User logged in: ' . $user->email,
        ]);

        toastr()->success('Logged in successfully!', 'Welcome!', ['timeOut' => 5000]);

        return redirect()->route(app(RoleManager::class)->dashboardRoute($user->role ?: 'customer'));
    }
}
