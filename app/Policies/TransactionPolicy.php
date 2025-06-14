<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    /**
     * Determine if the user can view any transactions.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'staff']);
    }

    /**
     * Determine if the user can view the transaction.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        // Staff can view all transactions
        if (in_array($user->role, ['admin', 'manager', 'staff'])) {
            return true;
        }

        // Members can only view their own transactions
        return $user->id === $transaction->member_id;
    }

    /**
     * Determine if the user can create transactions.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create transactions
    }

    /**
     * Determine if the user can update the transaction.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        // Only staff can update transactions
        if (in_array($user->role, ['admin', 'manager', 'staff'])) {
            return true;
        }

        // Members can only update their own pending transactions
        return $user->id === $transaction->member_id && $transaction->status === Transaction::STATUS_PENDING;
    }

    /**
     * Determine if the user can delete the transaction.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        // Only admins can delete transactions
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can approve transactions.
     */
    public function approve(User $user, ?Transaction $transaction = null): bool
    {
        // Only staff can approve transactions
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            return false;
        }

        // If no specific transaction, check general approval permission
        if (!$transaction) {
            return true;
        }

        // Cannot approve own transactions
        if ($user->id === $transaction->member_id) {
            return false;
        }

        // Can only approve pending transactions
        return $transaction->status === Transaction::STATUS_PENDING;
    }

    /**
     * Determine if the user can reject transactions.
     */
    public function reject(User $user, Transaction $transaction): bool
    {
        return $this->approve($user, $transaction);
    }

    /**
     * Determine if the user can perform bulk operations.
     */
    public function bulkApprove(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'staff']);
    }

    /**
     * Determine if the user can perform bulk reject operations.
     */
    public function bulkReject(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'staff']);
    }

    /**
     * Determine if the user can view approval statistics.
     */
    public function viewApprovalStats(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'staff']);
    }

    /**
     * Determine if the user can access the approval dashboard.
     */
    public function viewApprovalDashboard(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'staff']);
    }
} 