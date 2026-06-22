<?php

namespace App\Services;

class RoleManager
{
    public function roles()
    {
        return config('foodops.roles', []);
    }

    public function level($role)
    {
        $roles = $this->roles();

        return isset($roles[$role]) ? $roles[$role]['level'] : 0;
    }

    public function label($role)
    {
        $roles = $this->roles();

        return isset($roles[$role]) ? $roles[$role]['label'] : ucfirst(str_replace('_', ' ', $role));
    }

    public function badge($role)
    {
        $roles = $this->roles();

        return isset($roles[$role]) ? $roles[$role]['badge'] : 'bg-secondary text-white';
    }

    public function dashboardRoute($role)
    {
        $roles = $this->roles();

        if (isset($roles[$role]['dashboard_route'])) {
            return $roles[$role]['dashboard_route'];
        }

        return 'customer.dashboard';
    }

    public function autoPromotedRoleForEmail($email)
    {
        $normalized = strtolower(trim($email));
        $emails = config('foodops.auto_promote_emails', []);

        foreach ($emails as $role => $roleEmails) {
            $normalizedRoleEmails = array_map('strtolower', $roleEmails);

            if (in_array($normalized, $normalizedRoleEmails, true)) {
                return $role;
            }
        }

        return null;
    }

    public function hasMinimumRole($userRole, $requiredRole)
    {
        return $this->level($userRole) >= $this->level($requiredRole);
    }

    public function canAccessAny($userRole, array $requiredRoles)
    {
        foreach ($requiredRoles as $requiredRole) {
            if ($this->hasMinimumRole($userRole, $requiredRole)) {
                return true;
            }
        }

        return false;
    }

    public function canManageRoles($actorRole)
    {
        return in_array($actorRole, ['super_admin', 'developer'], true);
    }

    public function assignableRolesFor($actorRole)
    {
        $roles = array_keys($this->roles());

        if ($actorRole === 'developer') {
            return $roles;
        }

        if ($actorRole === 'super_admin') {
            return array_values(array_diff($roles, ['developer']));
        }

        return ['customer'];
    }

    public function navigationFor($role)
    {
        return config('foodops.dashboard_navigation.' . $role, []);
    }

    public function customerBottomNavigation()
    {
        return config('foodops.customer_bottom_navigation', []);
    }

    public function statusLabel($status)
    {
        $statuses = config('foodops.order_status_pipeline', []);

        return isset($statuses[$status]['label']) ? $statuses[$status]['label'] : ucfirst(str_replace('_', ' ', $status));
    }

    public function statusNext($status)
    {
        $statuses = config('foodops.order_status_pipeline', []);

        return isset($statuses[$status]['next']) ? $statuses[$status]['next'] : null;
    }

    public function statusBadge($status)
    {
        $badges = config('foodops.status_badges', []);

        return isset($badges[$status]) ? $badges[$status] : 'bg-secondary text-white';
    }
}
