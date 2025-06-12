<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin role with all permissions
        Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
                'description' => 'Full system access with all permissions',
                'permissions' => Role::PERMISSIONS
            ]
        );

        // Staff role with operational permissions
        Role::updateOrCreate(
            ['slug' => 'staff'],
            [
                'name' => 'Staff',
                'description' => 'Staff member with operational permissions',
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
                    'edit-loans',
                    'approve-loans',
                    'disburse-loans',
                    'view-branches',
                    'view-reports',
                ]
            ]
        );

        // Member role with limited permissions
        Role::updateOrCreate(
            ['slug' => 'member'],
            [
                'name' => 'Member',
                'description' => 'SACCO member with basic access',
                'permissions' => [
                    'view-accounts', // Can view their own accounts
                    'view-loans',    // Can view their own loans
                ]
            ]
        );

        // Manager role with advanced permissions but not system settings
        Role::updateOrCreate(
            ['slug' => 'manager'],
            [
                'name' => 'Manager',
                'description' => 'Branch manager with advanced permissions',
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
                    'edit-loans',
                    'approve-loans',
                    'disburse-loans',
                    'view-branches',
                    'manage-branches',
                    'view-reports',
                    'export-reports',
                ]
            ]
        );
    }
} 