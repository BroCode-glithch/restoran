<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use App\Services\RoleManager;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $roleManager = app(RoleManager::class);
        $promotedRole = $roleManager->autoPromotedRoleForEmail($data['email']);

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => isset($data['phone']) ? $data['phone'] : null,
            'role' => $promotedRole ?: 'customer',
            'business_id' => currentBusinessId(),
            'is_active' => true,
        ]);
    }

    /**
    * Display toast after successful registration.
    *
    * @return string
    */
    protected function registered(Request $request, $user)
    {
        SystemLog::create([
            'business_id' => $user->business_id,
            'actor_user_id' => $user->id,
            'level' => 'info',
            'category' => 'auth',
            'message' => 'User registered: ' . $user->email,
        ]);

        toastr()->success('Registration successful! You can now log in.', 'Welcome!', ['timeOut' => 5000]);

        return redirect()->route(app(RoleManager::class)->dashboardRoute($user->role ?: 'customer'));
    }
}
