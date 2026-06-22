<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Services\RoleManager;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'business_id',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->business_id)) {
                $user->business_id = currentBusinessId();
            }

            $promotedRole = app(RoleManager::class)->autoPromotedRoleForEmail($user->email);

            if ($promotedRole) {
                $user->role = $promotedRole;
            }

            if (empty($user->role)) {
                $user->role = 'customer';
            }

            if (!isset($user->is_active)) {
                $user->is_active = true;
            }
        });
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function assignedOrders()
    {
        return $this->hasMany(Order::class, 'assigned_staff_id');
    }

    public function dashboardRoute()
    {
        return roleDashboardRoute($this->role ?: 'customer');
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function canAccessRole($requiredRole)
    {
        return app(RoleManager::class)->hasMinimumRole($this->role ?: 'customer', $requiredRole);
    }

    public function isDeveloper()
    {
        return $this->role === 'developer';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isKitchenStaff()
    {
        return $this->role === 'kitchen_staff';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }
}
