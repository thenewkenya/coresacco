# SACCO Role Management System

## Overview

The SACCO Core Management System includes a comprehensive role-based access control (RBAC) system that allows administrators to manage user permissions and access levels throughout the application.

## Features

### ✅ Complete Role Management
- **CRUD Operations**: Create, read, update, and delete roles
- **Permission Management**: Assign granular permissions to roles
- **User Assignment**: Assign/remove roles from users
- **System Protection**: Prevents deletion of critical system roles
- **Bulk Operations**: Bulk assign roles to multiple users

### ✅ Advanced User Interface
- **Interactive Dashboard**: Statistics and role overview
- **Search & Filtering**: Filter users by roles, search by name/email
- **Real-time Updates**: Dynamic permission counting and validation
- **Responsive Design**: Works on desktop and mobile devices
- **Dark Mode Support**: Full dark mode compatibility

### ✅ Security Features
- **Authorization Policies**: Role-based access to management functions
- **System Role Protection**: Admin, Manager, Staff, Member roles are protected
- **Last Admin Protection**: Prevents removing the last admin user
- **Validation**: Comprehensive form validation and error handling

## System Roles

| Role | Description | Default Permissions |
|------|-------------|-------------------|
| **Admin** | System administrator with full access | All permissions |
| **Manager** | Branch manager with advanced operations | Most operational permissions + branch management |
| **Staff** | SACCO staff member for daily operations | Member, account, and loan operations |
| **Member** | SACCO member with basic access | View own accounts and loans |

## Permission Categories

### Member Management
- `view-members` - Can view member information
- `create-members` - Can register new members
- `edit-members` - Can modify member details
- `delete-members` - Can remove members

### Account Management
- `view-accounts` - Can view account details
- `create-accounts` - Can open new accounts
- `edit-accounts` - Can modify account settings
- `delete-accounts` - Can close accounts
- `process-transactions` - Can process deposits/withdrawals

### Loan Management
- `view-loans` - Can view loan information
- `create-loans` - Can create loan applications
- `edit-loans` - Can modify loan details
- `delete-loans` - Can delete loan records
- `approve-loans` - Can approve/reject loans
- `disburse-loans` - Can disburse approved loans

### Branch Management
- `view-branches` - Can view branch information
- `manage-branches` - Can manage branch operations

### Reports & Settings
- `view-reports` - Can access reports
- `export-reports` - Can export report data
- `manage-settings` - Can modify system settings
- `manage-roles` - Can manage user roles

## User Interface

### Dashboard (`/roles`)
- **Statistics Cards**: Total users, roles, admins, members
- **Role Grid**: Visual overview of all roles with user counts
- **User Assignment Section**: Manage user role assignments
- **Search & Filter**: Find users and filter by roles

### Role Management

#### Create Role (`/roles/create`)
- **Role Information**: Name and description
- **Permission Selection**: Grouped permissions with descriptions
- **Quick Actions**: Select all/clear all permissions
- **Real-time Validation**: Live permission count and validation

#### View Role (`/roles/{role}`)
- **Role Details**: Information and statistics
- **Permission Display**: Grouped permissions with descriptions
- **Assigned Users**: List of users with this role
- **Quick Actions**: Assign/remove users, edit role

#### Edit Role (`/roles/{role}/edit`)
- **Form Pre-filled**: Current role data
- **Permission Management**: Update role permissions
- **User Statistics**: Current assignment information
- **Danger Zone**: Delete role (if allowed)

## API Endpoints

### RESTful Routes
```php
GET    /roles              # List all roles
GET    /roles/create       # Show create form
POST   /roles              # Store new role
GET    /roles/{role}       # Show role details
GET    /roles/{role}/edit  # Show edit form
PUT    /roles/{role}       # Update role
DELETE /roles/{role}       # Delete role
```

### Role Assignment
```php
POST /roles/assign        # Assign role to user
POST /roles/remove        # Remove role from user
POST /roles/bulk-assign   # Bulk assign roles
```

### AJAX Endpoints
```php
GET  /roles/{role}/permissions      # Get role permissions
POST /roles/{role}/permissions      # Update permissions
GET  /roles/{role}/available-users  # Get unassigned users
```

## Authorization

### Policies
The system uses Laravel policies for authorization:

```php
// View roles (Admin, Manager)
$user->can('viewAny', Role::class)

// Create roles (Admin only)
$user->can('create', Role::class)

// Update roles (Admin only)
$user->can('update', $role)

// Delete roles (Admin only, with restrictions)
$user->can('delete', $role)
```

### Middleware Protection
Routes are protected with middleware:
- `role:admin` - Admin only access
- `auth` - Authenticated users only

## Database Schema

### Roles Table
```sql
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    permissions JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Role User Pivot Table
```sql
CREATE TABLE role_user (
    role_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    PRIMARY KEY (role_id, user_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Usage Examples

### Check User Permissions
```php
// Check if user has specific role
$user->hasRole('admin')

// Check if user has any of multiple roles
$user->hasAnyRole(['admin', 'manager'])

// Check if user has specific permission
$user->hasPermission('view-members')

// Check if user has any of multiple permissions
$user->hasAnyPermission(['view-members', 'create-members'])
```

### Assign Roles Programmatically
```php
// Assign role to user
$user = User::find(1);
$role = Role::where('slug', 'staff')->first();
$user->roles()->attach($role);

// Remove role from user
$user->roles()->detach($role);

// Sync roles (replace all current roles)
$user->roles()->sync([$role1->id, $role2->id]);
```

### Create Custom Roles
```php
$role = Role::create([
    'name' => 'Branch Supervisor',
    'slug' => 'branch-supervisor',
    'description' => 'Supervises branch operations',
    'permissions' => [
        'view-members',
        'create-members',
        'edit-members',
        'view-accounts',
        'process-transactions',
        'view-reports'
    ]
]);
```

## Blade Directives

The system provides custom Blade directives for template authorization:

```blade
{{-- Check specific permission --}}
@can('view-members')
    <button>View Members</button>
@endcan

{{-- Check multiple permissions --}}
@canany('view-members', 'create-members')
    <div>Member Management</div>
@endcanany

{{-- Check specific role --}}
@role('admin')
    <button>Admin Panel</button>
@endrole

{{-- Check multiple roles --}}
@roleany('admin', 'manager')
    <div>Management Section</div>
@endroleany
```

## Setup & Installation

### 1. Run Role Setup Command
```bash
php artisan sacco:setup-roles --admin-email=admin@example.com --admin-password=secure123
```

### 2. Seed Additional Users (Optional)
```bash
php artisan db:seed --class=UserSeeder
```

### 3. Verify Installation
Visit `/roles` in your application to access the role management interface.

## Security Best Practices

### 1. Principle of Least Privilege
- Assign minimum permissions required for each role
- Regularly review and audit role permissions
- Remove unnecessary permissions promptly

### 2. Role Separation
- Don't mix operational roles with member roles
- Create specific roles for specific functions
- Avoid overly broad permissions

### 3. Regular Auditing
- Monitor role assignments and changes
- Log permission modifications
- Review inactive users and their roles

### 4. System Role Protection
- Never delete system roles (admin, member, staff, manager)
- Maintain at least one admin user at all times
- Protect critical permissions

## Troubleshooting

### Common Issues

#### 1. Permission Denied Errors
- Verify user has been assigned correct role
- Check role has required permissions
- Ensure middleware is properly configured

#### 2. Role Assignment Issues
- Confirm role exists in database
- Verify user exists and is active
- Check for duplicate role assignments

#### 3. UI/Display Issues
- Clear browser cache
- Check for JavaScript errors
- Verify Flux UI components are loaded

#### 4. Database Issues
- Run migrations: `php artisan migrate`
- Seed roles: `php artisan db:seed --class=RoleSeeder`
- Check foreign key constraints

### Support Commands

```bash
# Reset roles to default
php artisan sacco:setup-roles

# Clear all caches
php artisan optimize:clear

# Check current user roles
php artisan tinker --execute="User::with('roles')->get()"

# Verify permissions
php artisan tinker --execute="Role::with('users')->get()"
```

## Future Enhancements

### Planned Features
- [ ] Permission inheritance system
- [ ] Temporary role assignments with expiry
- [ ] Role templates for quick setup
- [ ] Advanced permission conditions
- [ ] Activity logging and audit trails
- [ ] Role-based dashboard customization

### Integration Opportunities
- [ ] LDAP/Active Directory integration
- [ ] SSO (Single Sign-On) support
- [ ] Mobile app role management
- [ ] API key role assignments
- [ ] Two-factor authentication for role changes

---

**Version**: 1.0  
**Last Updated**: December 2024  
**Maintainer**: SACCO Core Development Team 