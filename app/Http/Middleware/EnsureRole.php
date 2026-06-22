<?php

namespace App\Http\Middleware;

use App\Services\RoleManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (empty($roles)) {
            return $next($request);
        }

        $roleManager = app(RoleManager::class);

        foreach ($roles as $requiredRole) {
            if ($roleManager->hasMinimumRole($user->role ?: 'customer', $requiredRole)) {
                return $next($request);
            }
        }

        abort(403, 'You do not have access to this area.');
    }
}
