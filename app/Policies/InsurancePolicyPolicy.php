<?php

namespace App\Policies;

use App\Models\InsurancePolicy;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InsurancePolicyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'manager', 'agent']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InsurancePolicy $insurancePolicy): bool
    {
        return $user->hasRole(['admin', 'manager', 'agent']) || 
               $user->id === $insurancePolicy->member_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // All users can create insurance policies
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InsurancePolicy $insurancePolicy): bool
    {
        return $user->hasRole(['admin', 'manager', 'agent']) || 
               $user->id === $insurancePolicy->member_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InsurancePolicy $insurancePolicy): bool
    {
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, InsurancePolicy $insurancePolicy): bool
    {
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, InsurancePolicy $insurancePolicy): bool
    {
        return $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can manage insurance policies.
     */
    public function manage(User $user): bool
    {
        return $user->hasRole(['admin', 'manager', 'agent']);
    }
} 