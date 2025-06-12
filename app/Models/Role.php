<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions'
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

    // Define available permissions
    public const PERMISSIONS = [
        // Member management
        'view-members',
        'create-members',
        'edit-members',
        'delete-members',
        
        // Account management
        'view-accounts',
        'create-accounts',
        'edit-accounts',
        'process-transactions',
        
        // Loan management
        'view-loans',
        'create-loans',
        'approve-loans',
        'disburse-loans',
        
        // Branch management
        'view-branches',
        'manage-branches',
        
        // Reports
        'view-reports',
        'export-reports',
        
        // System settings
        'manage-settings',
        'manage-roles'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }
} 