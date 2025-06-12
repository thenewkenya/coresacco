# SACCO Role & Permission System

This document explains how to use the role and permission system implemented in your SACCO management application.

## Quick Setup

Run the setup command to create roles and optionally an admin user:

```bash
php artisan sacco:setup-roles --admin-email=admin@sacco.com --admin-password=secure123
```

Or run interactively:
```bash
php artisan sacco:setup-roles
```

## System Overview

### Roles Available

| Role | Description | Access Level |
|------|-------------|--------------|
| **Admin** | System administrator | Full access to all features |
| **Manager** | Branch manager | Advanced operations + branch management |
| **Staff** | SACCO staff member | Member/account/loan operations |
| **Member** | SACCO member | View own accounts and loans |

### Permissions

```php
// Member management
'view-members', 'create-members', 'edit-members', 'delete-members'

// Account management  
'view-accounts', 'create-accounts', 'edit-accounts', 'delete-accounts', 'process-transactions'

// Loan management
'view-loans', 'create-loans', 'edit-loans', 'delete-loans', 'approve-loans', 'disburse-loans'

// Branch management
'view-branches', 'manage-branches'

// Reports & Settings
'view-reports', 'export-reports', 'manage-settings', 'manage-roles'
```

## Usage Examples

### 1. API Routes (Protected)

Your API routes are now protected with permission middleware:

```php
// Only users with 'view-members' permission can access
GET /api/members

// Only users with 'create-loans' permission can create loans
POST /api/loans

// Only users with 'approve-loans' permission can approve loans
POST /api/loans/{loan}/approve
```

### 2. Livewire Components

Use the `HasPermissions` trait in your Livewire components:

```php
<?php

namespace App\Livewire;

use App\Traits\HasPermissions;
use Livewire\Component;

class MemberManager extends Component
{
    use HasPermissions;

    public function mount()
    {
        // Check permission on component load
        $this->authorize('view-members');
    }

    public function createMember()
    {
        // Check permission before action
        $this->authorize('create-members');
        
        // Your logic here...
    }

    public function render()
    {
        return view('livewire.member-manager', [
            'canCreate' => $this->can('create-members'),
            'canEdit' => $this->can('edit-members'),
            'canDelete' => $this->can('delete-members'),
        ]);
    }
}
```

### 3. Blade Templates

Use custom Blade directives in your views:

```blade
{{-- Check single permission --}}
@can('create-members')
    <button>Add New Member</button>
@endcan

{{-- Check multiple permissions (any) --}}
@canany('edit-members', 'delete-members')
    <div>Member Actions Available</div>
@endcanany

{{-- Check role --}}
@role('admin')
    <div>Admin Panel</div>
@endrole

{{-- Check multiple roles (any) --}}
@roleany('staff', 'manager')
    <div>Staff Operations</div>
@endroleany
```

### 4. Controller Methods

Protect controller methods with middleware:

```php
public function __construct()
{
    $this->middleware('permission:view-members')->only(['index', 'show']);
    $this->middleware('permission:create-members')->only(['store']);
    $this->middleware('permission:edit-members')->only(['update']);
    $this->middleware('permission:delete-members')->only(['destroy']);
}
```

Or check in methods directly:

```php
public function approveApplication(Request $request)
{
    if (!auth()->user()->hasPermission('approve-loans')) {
        return response()->json(['message' => 'Access denied'], 403);
    }
    
    // Your logic here...
}
```

### 5. User Model Methods

Available methods on User model:

```php
$user = auth()->user();

// Check permissions
$user->hasPermission('view-members');
$user->hasAnyPermission(['edit-members', 'delete-members']);

// Check roles
$user->hasRole('admin');
$user->hasAnyRole(['staff', 'manager']);

// Convenience methods
$user->isAdmin();
$user->isStaff(); 
$user->isMember();
```

## Default Role Assignment

New users automatically receive the "member" role upon registration. This is handled in `resources/views/livewire/auth/register.blade.php`.

## Managing Roles & Permissions

### Assign Role to User

```php
$user = User::find(1);
$role = Role::where('slug', 'staff')->first();
$user->roles()->attach($role);
```

### Remove Role from User

```php
$user->roles()->detach($role);
```

### Update Role Permissions

```php
$role = Role::find(1);
$role->permissions = ['view-members', 'create-members'];
$role->save();
```

## Security Best Practices

1. **Principle of Least Privilege**: Assign minimum permissions needed
2. **Regular Audits**: Review user roles and permissions regularly
3. **Role Separation**: Don't mix operational roles (staff/manager) with member roles
4. **API Security**: Always use middleware on API routes
5. **Frontend Security**: Hide UI elements based on permissions

## Middleware Usage

### Route Groups

```php
Route::middleware(['auth:sanctum', 'permission:view-reports'])->group(function () {
    Route::get('/reports/daily', [ReportController::class, 'daily']);
    Route::get('/reports/monthly', [ReportController::class, 'monthly']);
});
```

### Individual Routes

```php
Route::post('/loans/{loan}/approve', [LoanController::class, 'approve'])
    ->middleware(['auth:sanctum', 'permission:approve-loans']);
```

## Error Handling

The system provides appropriate error responses:

- **401 Unauthorized**: User not authenticated
- **403 Forbidden**: User lacks required permission/role

Handle these in your frontend:

```javascript
// Example API call handling
fetch('/api/members', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
    }
})
.then(response => {
    if (response.status === 403) {
        showError('You do not have permission to view members');
    }
    return response.json();
});
```

## Development Tips

1. **Testing**: Create test users with different roles for development
2. **Seeding**: Use the RoleSeeder to set up consistent role structure
3. **Debugging**: Check user permissions with `auth()->user()->roles->pluck('permissions')->flatten()`
4. **Performance**: Consider caching role/permission queries for high-traffic applications

## Role Hierarchy

```
Admin (All Permissions)
├── Manager (Advanced Operations)
├── Staff (Basic Operations)  
└── Member (Read-Only Own Data)
```

## Troubleshooting

### Permission Denied Errors
- Verify user has been assigned correct role
- Check role has required permissions
- Ensure middleware is properly configured

### Registration Issues
- Verify 'member' role exists in database
- Check role seeder has been run
- Confirm registration logic assigns default role

### API Access Issues
- Verify Sanctum token is valid
- Check route middleware configuration
- Confirm user authentication

---

This system provides comprehensive role-based access control for your SACCO management application. For additional features or modifications, refer to the Role and User models. 