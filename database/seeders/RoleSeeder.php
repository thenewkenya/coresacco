<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Role
        Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'System administrator with full access',
            'permissions' => Role::PERMISSIONS
        ]);

        // Staff Role
        Role::create([
            'name' => 'Staff',
            'slug' => 'staff',
            'description' => 'SACCO staff member',
            'permissions' => [
                'view-members',
                'create-members',
                'edit-members',
                'view-accounts',
                'create-accounts',
                'process-transactions',
                'view-loans',
                'create-loans',
                'view-branches',
                'view-reports'
            ]
        ]);

        // Branch Manager Role
        Role::create([
            'name' => 'Branch Manager',
            'slug' => 'branch-manager',
            'description' => 'Branch manager with elevated permissions',
            'permissions' => [
                'view-members',
                'create-members',
                'edit-members',
                'view-accounts',
                'create-accounts',
                'edit-accounts',
                'process-transactions',
                'view-loans',
                'create-loans',
                'approve-loans',
                'disburse-loans',
                'view-branches',
                'manage-branches',
                'view-reports',
                'export-reports'
            ]
        ]);

        // Member Role
        Role::create([
            'name' => 'Member',
            'slug' => 'member',
            'description' => 'Regular SACCO member',
            'permissions' => [
                'view-accounts',
                'view-loans',
                'create-loans'
            ]
        ]);
    }
} 