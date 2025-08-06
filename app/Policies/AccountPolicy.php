<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AccountPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('staff') || $user->hasRole('manager');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Account $account): bool
    {
        // Users can view their own accounts, staff can view any
        return $user->id === $account->member_id || $user->hasRole('admin') || $user->hasRole('staff') || $user->hasRole('manager');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Members can create accounts for themselves, staff can create for any member
        return $user->hasRole('admin') || $user->hasRole('staff') || $user->hasRole('manager') || $user->hasRole('member');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Account $account): bool
    {
        // Only staff can update accounts
        return $user->hasRole('admin') || $user->hasRole('staff') || $user->hasRole('manager');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Account $account): bool
    {
        // Only admins can delete accounts, and only if balance is zero
        return $user->hasRole('admin') && $account->balance == 0;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Account $account): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Account $account): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can make transactions on the account.
     */
    public function transact(User $user, Account $account): bool
    {
        // Users can transact on their own active accounts, staff can on any active account
        $canAccess = $user->id === $account->member_id || $user->hasRole('admin') || $user->hasRole('staff') || $user->hasRole('manager');
        return $canAccess && $account->status === 'active';
    }

    /**
     * Determine whether the user can manage the account (update status, etc).
     */
    public function manage(User $user, ?Account $account = null): bool
    {
        return $user->hasRole('admin') || $user->hasRole('staff') || $user->hasRole('manager');
    }
}
