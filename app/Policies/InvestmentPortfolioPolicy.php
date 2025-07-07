<?php

namespace App\Policies;

use App\Models\InvestmentPortfolio;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvestmentPortfolioPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_investments') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InvestmentPortfolio $investmentPortfolio): bool
    {
        return $user->id === $investmentPortfolio->member_id || 
               $user->hasPermission('view_all_investments') || 
               $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_investments') || $user->hasRole('member');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InvestmentPortfolio $investmentPortfolio): bool
    {
        return $user->id === $investmentPortfolio->member_id || 
               $user->hasPermission('manage_investments') || 
               $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InvestmentPortfolio $investmentPortfolio): bool
    {
        return $user->hasPermission('delete_investments') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, InvestmentPortfolio $investmentPortfolio): bool
    {
        return $user->hasPermission('restore_investments') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, InvestmentPortfolio $investmentPortfolio): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage investments.
     */
    public function manage(User $user): bool
    {
        return $user->hasPermission('manage_investments') || $user->hasRole('admin');
    }
}
