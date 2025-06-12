<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'phone',
        'email',
        'manager_id',
        'status',
        'opening_date',
        'working_hours',
        'coordinates',
    ];

    protected $casts = [
        'opening_date' => 'date',
        'working_hours' => 'array',
        'coordinates' => 'array',
    ];

    // Branch statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_UNDER_MAINTENANCE = 'under_maintenance';

    // Relationships
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getTotalMembers(): int
    {
        return $this->members()->count();
    }

    public function getTotalActiveLoans(): int
    {
        return $this->members()
            ->whereHas('loans', function ($query) {
                $query->where('status', Loan::STATUS_ACTIVE);
            })
            ->count();
    }

    public function generateBranchCode(): string
    {
        return 'BR' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
} 