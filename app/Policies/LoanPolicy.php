<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LoanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff', 'manager']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Loan $loan): bool
    {
        // Users can view their own loans, staff can view any
        return $user->id === $loan->member_id || $user->hasAnyRole(['admin', 'staff', 'manager']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Members can apply for loans, staff can create loans for members
        return true; // Any authenticated user can apply
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Loan $loan): bool
    {
        // Only staff can update loans, members can only update pending applications
        if ($user->hasAnyRole(['admin', 'staff', 'manager'])) {
            return true;
        }
        
        // Members can only update their own pending applications
        return $user->id === $loan->member_id && $loan->status === 'pending';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Loan $loan): bool
    {
        // Only admins can delete loans, and only if pending or rejected
        return $user->hasRole('admin') && in_array($loan->status, ['pending', 'rejected']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Loan $loan): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Loan $loan): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can approve the loan.
     */
    public function approve(User $user, Loan $loan): bool
    {
        return $user->hasAnyRole(['admin', 'manager']) && $loan->status === 'pending';
    }

    /**
     * Determine whether the user can reject the loan.
     */
    public function reject(User $user, Loan $loan): bool
    {
        return $user->hasAnyRole(['admin', 'manager']) && $loan->status === 'pending';
    }

    /**
     * Determine whether the user can make repayments on the loan.
     */
    public function repay(User $user, Loan $loan): bool
    {
        // Users can repay their own active loans, staff can process any active loan repayment
        $canAccess = $user->id === $loan->member_id || $user->hasAnyRole(['admin', 'staff', 'manager']);
        return $canAccess && $loan->status === 'active';
    }

    /**
     * Determine whether the user can disburse the loan.
     */
    public function disburse(User $user, Loan $loan): bool
    {
        return $user->hasAnyRole(['admin', 'manager']) && $loan->status === 'approved';
    }
}
