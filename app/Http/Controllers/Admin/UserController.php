<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SystemLog;
use App\Services\RoleManager;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(RoleManager $roleManager)
    {
        $perPage = $this->perPage(request());

        return view('admin.users.index', [
            'users' => User::query()->where('business_id', currentBusinessId())->latest()->paginate($perPage)->withQueryString(),
            'roles' => $roleManager->assignableRolesFor(auth()->user()->role ?: 'customer'),
            'canManageRoles' => $roleManager->canManageRoles(auth()->user()->role ?: 'customer'),
            'perPage' => $perPage,
        ]);
    }

    public function update(Request $request, User $user, RoleManager $roleManager)
    {
        $this->authorizeUser($user);

        if (!$roleManager->canManageRoles(auth()->user()->role ?: 'customer')) {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            abort(403, 'Self role changes are not allowed.');
        }

        $validRoles = $roleManager->assignableRolesFor(auth()->user()->role ?: 'customer');

        $data = $request->validate([
            'role' => ['required', 'in:' . implode(',', $validRoles)],
            'is_active' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user->update([
            'role' => $data['role'],
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $user->is_active,
            'phone' => $request->has('phone') ? $data['phone'] : $user->phone,
        ]);

        SystemLog::query()->create([
            'business_id' => currentBusinessId(),
            'actor_user_id' => auth()->id(),
            'level' => 'info',
            'category' => 'users',
            'message' => 'Role updated for ' . $user->email . ' to ' . $user->role,
        ]);

        toastr()->success('User updated.', 'Saved', ['timeOut' => 3000]);

        return redirect()->route('admin.users.index');
    }

    protected function authorizeUser(User $user)
    {
        if ($user->business_id !== currentBusinessId()) {
            abort(404);
        }
    }

    protected function perPage(Request $request)
    {
        $allowed = [10, 20, 50];
        $perPage = (int) $request->query('per_page', 10);

        return in_array($perPage, $allowed, true) ? $perPage : 10;
    }
}
