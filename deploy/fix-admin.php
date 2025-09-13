<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING ADMIN USER ROLES ON PRODUCTION ===\n";

try {
    // Find or create admin user
    $admin = \App\Models\User::where('email', 'admin@esacco.com')->first();
    
    if (!$admin) {
        echo "Admin user not found. Creating...\n";
        $admin = \App\Models\User::create([
            'name' => 'System Administrator',
            'email' => 'admin@esacco.com',
            'password' => \Illuminate\Support\Facades\Hash::make('AdminPassword123!'),
            'email_verified_at' => now(),
            'membership_status' => 'active',
            'role' => 'admin',
        ]);
        echo "Admin user created: {$admin->name} ({$admin->email})\n";
    } else {
        echo "Admin user found: {$admin->name} ({$admin->email})\n";
    }

    // Find or create admin role
    $adminRole = \App\Models\Role::where('slug', 'admin')->first();
    
    if (!$adminRole) {
        echo "Admin role not found. Creating...\n";
        $adminRole = \App\Models\Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Full system access with all permissions',
            'permissions' => \App\Models\Role::PERMISSIONS
        ]);
        echo "Admin role created with " . count(\App\Models\Role::PERMISSIONS) . " permissions\n";
    } else {
        echo "Admin role found: {$adminRole->name}\n";
    }

    // Check if user already has admin role
    if ($admin->roles()->where('role_id', $adminRole->id)->exists()) {
        echo "Admin user already has admin role.\n";
    } else {
        // Assign admin role
        $admin->roles()->attach($adminRole);
        echo "Admin role assigned successfully!\n";
    }

    // Verify the assignment
    $admin->refresh();
    echo "\n=== VERIFICATION ===\n";
    echo "Current roles: " . $admin->roles->pluck('name')->join(', ') . "\n";
    echo "Has admin role: " . ($admin->hasRole('admin') ? 'Yes' : 'No') . "\n";
    echo "Is admin: " . ($admin->isAdmin() ? 'Yes' : 'No') . "\n";
    echo "Has permission 'view-members': " . ($admin->hasPermission('view-members') ? 'Yes' : 'No') . "\n";
    echo "Has permission 'manage-settings': " . ($admin->hasPermission('manage-settings') ? 'Yes' : 'No') . "\n";
    
    echo "\n=== ALL ROLES IN SYSTEM ===\n";
    $allRoles = \App\Models\Role::all();
    foreach ($allRoles as $role) {
        echo "- {$role->name} ({$role->slug}): " . count($role->permissions ?? []) . " permissions\n";
    }
    
    echo "\n✅ Admin user setup complete!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
