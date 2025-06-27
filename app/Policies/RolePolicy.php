<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Determine whether the user can view any roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can view the role.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can create roles.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the role.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the role.
     */
    public function delete(User $user, Role $role): bool
    {
        // Only admins can delete roles, and system roles cannot be deleted
        if (!$user->hasRole('admin')) {
            return false;
        }

        // Prevent deletion of system roles
        if (in_array($role->slug, ['admin', 'member', 'staff', 'manager'])) {
            return false;
        }

        // Prevent deletion of roles that have users assigned
        if ($role->users()->count() > 0) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the role.
     */
    public function restore(User $user, Role $role): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the role.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can assign roles to users.
     */
    public function assign(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can remove roles from users.
     */
    public function remove(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage permissions for roles.
     */
    public function managePermissions(User $user, Role $role): bool
    {
        return $user->hasRole('admin');
    }
} 