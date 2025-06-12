<?php

/* Insurance manages insurance policies, 
tracks coverage, premiums, and claims
and includes policy status and renewal methods
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Insurance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'policy_number',
        'member_id',
        'insurance_type',
        'coverage_amount',
        'premium_amount',
        'start_date',
        'end_date',
        'status',
        'beneficiaries',
        'terms_conditions',
    ];

    protected $casts = [
        'coverage_amount' => 'decimal:2',
        'premium_amount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'beneficiaries' => 'array',
        'terms_conditions' => 'array',
    ];

    // Insurance types
    const TYPE_LIFE = 'life';
    const TYPE_HEALTH = 'health';
    const TYPE_PROPERTY = 'property';
    const TYPE_BUSINESS = 'business';

    // Insurance statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_CLAIMED = 'claimed';

    // Relationships
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               $this->end_date > now();
    }

    public function isExpired(): bool
    {
        return $this->end_date < now();
    }

    public function daysUntilExpiry(): int
    {
        return now()->diffInDays($this->end_date, false);
    }

    public function calculateRenewalPremium(): float
    {
        // Basic implementation - can be extended based on business rules
        return $this->premium_amount;
    }
} 