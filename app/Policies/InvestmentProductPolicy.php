<?php

namespace App\Policies;

use App\Models\InvestmentProduct;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvestmentProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All users can view products
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InvestmentProduct $investmentProduct): bool
    {
        return true; // All users can view individual products
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_investment_products') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InvestmentProduct $investmentProduct): bool
    {
        return $user->hasPermission('manage_investment_products') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InvestmentProduct $investmentProduct): bool
    {
        return $user->hasPermission('delete_investment_products') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, InvestmentProduct $investmentProduct): bool
    {
        return $user->hasPermission('restore_investment_products') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, InvestmentProduct $investmentProduct): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage investment products.
     */
    public function manage(User $user, InvestmentProduct $investmentProduct = null): bool
    {
        return $user->hasPermission('manage_investment_products') || $user->hasRole('admin');
    }
}
