<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\User;

trait AssignsDefaultRole
{
    protected function assignDefaultRole(User $user): void
    {
        $memberRole = Role::where('slug', 'member')->first();
        
        if ($memberRole) {
            $user->roles()->attach($memberRole->id);
        }
    }
} 