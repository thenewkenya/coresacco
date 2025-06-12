<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasPermissions
{
    /**
     * Check if the current user has the required permission
     */
    public function authorize(string $permission): void
    {
        if (!Auth::check() || !Auth::user()->hasPermission($permission)) {
            $this->dispatch('permission-denied', [
                'message' => 'You do not have permission to perform this action.',
                'required_permission' => $permission
            ]);
            
            return;
        }
    }

    /**
     * Check if the current user has any of the required permissions
     */
    public function authorizeAny(array $permissions): void
    {
        if (!Auth::check() || !Auth::user()->hasAnyPermission($permissions)) {
            $this->dispatch('permission-denied', [
                'message' => 'You do not have permission to perform this action.',
                'required_permissions' => $permissions
            ]);
            
            return;
        }
    }

    /**
     * Check if the current user has the required role
     */
    public function authorizeRole(string $role): void
    {
        if (!Auth::check() || !Auth::user()->hasRole($role)) {
            $this->dispatch('permission-denied', [
                'message' => 'You do not have the required role to perform this action.',
                'required_role' => $role
            ]);
            
            return;
        }
    }

    /**
     * Check if user can perform action and return boolean
     */
    public function can(string $permission): bool
    {
        return Auth::check() && Auth::user()->hasPermission($permission);
    }

    /**
     * Check if user has role and return boolean
     */
    public function hasRole(string $role): bool
    {
        return Auth::check() && Auth::user()->hasRole($role);
    }
} 