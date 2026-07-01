<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use App\Providers\RouteServiceProvider;
use App\Services\RoleManager;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

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

    public function showLoginForm(Request $request)
    {
        $this->storeIntendedRoute($request);

        return view('auth.login');
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

        toastr()->success('Logged in successfully!', ['timeOut' => 5000], 'Welcome!');
        session()->flash('swal', [
            'type' => 'success',
            'title' => 'Welcome!',
            'message' => 'Logged in successfully!',
            'ok_text' => 'OK',
        ]);

        $intendedRoute = $this->pullIntendedRoute($user);

        if ($intendedRoute) {
            return redirect()->route($intendedRoute);
        }

        return redirect()->route(app(RoleManager::class)->dashboardRoute($user->role ?: 'customer'));
    }

    protected function storeIntendedRoute(Request $request)
    {
        $nextRoute = $request->query('next');

        if (!$nextRoute) {
            return;
        }

        if (!Route::has($nextRoute)) {
            return;
        }

        if (!Str::startsWith($nextRoute, ['catalog.', 'cart.', 'orders.', 'customer.'])) {
            return;
        }

        session(['foodops.post_auth_route' => $nextRoute]);
    }

    protected function pullIntendedRoute($user)
    {
        $nextRoute = session()->pull('foodops.post_auth_route');

        if (!$nextRoute) {
            return null;
        }

        if (!$user || !$user->isCustomer()) {
            return null;
        }

        return Route::has($nextRoute) ? $nextRoute : null;
    }
}
