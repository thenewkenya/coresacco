<?php

namespace App\Policies;

use App\Models\User;

class SystemPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can view system settings.
     */
    public function viewSettings(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update system settings.
     */
    public function updateSettings(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can reset system settings.
     */
    public function resetSettings(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
