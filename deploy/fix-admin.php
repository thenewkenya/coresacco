<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING ALL USER ROLES ON PRODUCTION ===\n";

try {
    // First, ensure all roles exist
    echo "Setting up all roles...\n";
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'RoleSeeder']);
    echo "All roles created/updated successfully!\n";

    // Define all roles with their permissions
    $roles = [
        'admin' => [
            'name' => 'Administrator',
            'description' => 'Full system access with all permissions',
            'permissions' => \App\Models\Role::PERMISSIONS
        ],
        'manager' => [
            'name' => 'Manager',
            'description' => 'Branch manager with most operational permissions',
            'permissions' => [
                'view-members', 'create-members', 'edit-members',
                'view-accounts', 'create-accounts', 'edit-accounts', 'process-transactions',
                'view-loans', 'create-loans', 'edit-loans', 'approve-loans', 'disburse-loans',
                'view-branches', 'manage-branches',
                'view-reports', 'export-reports'
            ]
        ],
        'staff' => [
            'name' => 'Staff',
            'description' => 'SACCO staff with operational permissions',
            'permissions' => [
                'view-members', 'create-members', 'edit-members',
                'view-accounts', 'create-accounts', 'edit-accounts', 'process-transactions',
                'view-loans', 'create-loans', 'edit-loans', 'approve-loans', 'disburse-loans',
                'view-branches', 'view-reports'
            ]
        ],
        'member' => [
            'name' => 'Member',
            'description' => 'SACCO member with basic access',
            'permissions' => [
                'view-accounts', 'create-accounts', 'view-loans'
            ]
        ]
    ];

    // Create or update all roles
    foreach ($roles as $slug => $roleData) {
        $role = \App\Models\Role::updateOrCreate(
            ['slug' => $slug],
            $roleData
        );
        echo "Role '{$role->name}' ({$slug}) ready with " . count($role->permissions ?? []) . " permissions\n";
    }

    // Handle admin user specifically
    echo "\n=== SETTING UP ADMIN USER ===\n";
    $admin = \App\Models\User::where('email', 'admin@coresacco.com')->first();
    
    if (!$admin) {
        echo "Admin user not found. Creating...\n";
        $admin = \App\Models\User::create([
            'name' => 'System Administrator',
            'email' => 'admin@coresacco.com',
            'password' => \Illuminate\Support\Facades\Hash::make('AdminPassword123!'),
            'email_verified_at' => now(),
            'membership_status' => 'active',
            'role' => 'admin',
        ]);
        echo "Admin user created: {$admin->name} ({$admin->email})\n";
    } else {
        echo "Admin user found: {$admin->name} ({$admin->email})\n";
    }

    // Assign admin role to admin user
    $adminRole = \App\Models\Role::where('slug', 'admin')->first();
    if ($adminRole && !$admin->roles()->where('role_id', $adminRole->id)->exists()) {
        $admin->roles()->attach($adminRole);
        echo "Admin role assigned to admin user!\n";
    } else {
        echo "Admin user already has admin role.\n";
    }

    // Fix all existing users - assign roles based on their 'role' field
    echo "\n=== FIXING ALL EXISTING USERS ===\n";
    $users = \App\Models\User::all();
    
    foreach ($users as $user) {
        echo "Processing user: {$user->name} ({$user->email})\n";
        
        // Clear existing roles first
        $user->roles()->detach();
        
        // Assign role based on user's 'role' field
        if ($user->role) {
            $role = \App\Models\Role::where('slug', $user->role)->first();
            if ($role) {
                $user->roles()->attach($role);
                echo "  - Assigned '{$role->name}' role\n";
            } else {
                echo "  - Warning: Role '{$user->role}' not found for user\n";
            }
        } else {
            // Default to member role if no role specified
            $memberRole = \App\Models\Role::where('slug', 'member')->first();
            if ($memberRole) {
                $user->roles()->attach($memberRole);
                echo "  - Assigned default 'member' role\n";
            }
        }
    }

    // Verify all users
    echo "\n=== VERIFICATION ===\n";
    $allUsers = \App\Models\User::with('roles')->get();
    
    foreach ($allUsers as $user) {
        echo "User: {$user->name} ({$user->email})\n";
        echo "  Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
        echo "  Has admin role: " . ($user->hasRole('admin') ? 'Yes' : 'No') . "\n";
        echo "  Has member role: " . ($user->hasRole('member') ? 'Yes' : 'No') . "\n";
        echo "  Has view-accounts permission: " . ($user->hasPermission('view-accounts') ? 'Yes' : 'No') . "\n";
        echo "\n";
    }
    
    echo "\n=== ALL ROLES IN SYSTEM ===\n";
    $allRoles = \App\Models\Role::all();
    foreach ($allRoles as $role) {
        echo "- {$role->name} ({$role->slug}): " . count($role->permissions ?? []) . " permissions\n";
    }
    
    echo "\n✅ All user roles setup complete!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
