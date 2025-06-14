<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any members.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'staff']);
    }

    /**
     * Determine whether the user can view the member.
     */
    public function view(User $user, User $member): bool
    {
        // Users can view their own profile
        if ($user->id === $member->id) {
            return true;
        }

        // Staff can view member profiles
        return in_array($user->role, ['admin', 'manager', 'staff']);
    }

    /**
     * Determine whether the user can create members.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'staff']);
    }

    /**
     * Determine whether the user can update the member.
     */
    public function update(User $user, User $member): bool
    {
        // Users can update their own profile (limited fields)
        if ($user->id === $member->id) {
            return true;
        }

        // Staff can update member profiles
        return in_array($user->role, ['admin', 'manager', 'staff']);
    }

    /**
     * Determine whether the user can delete the member.
     */
    public function delete(User $user, User $member): bool
    {
        // Only admins and managers can delete members
        return in_array($user->role, ['admin', 'manager']);
    }
} 