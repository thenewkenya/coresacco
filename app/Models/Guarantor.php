<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guarantor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'full_name',
        'id_number',
        'phone_number',
        'address',
        'employment_status',
        'monthly_income',
        'relationship_to_borrower',
        'status',
        'max_guarantee_amount',
        'current_guarantee_obligations',
    ];

    protected $casts = [
        'monthly_income' => 'decimal:2',
        'max_guarantee_amount' => 'decimal:2',
        'current_guarantee_obligations' => 'decimal:2',
        'status' => 'string',
    ];

    // Guarantor statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_BLACKLISTED = 'blacklisted';

    // Employment statuses
    const EMPLOYMENT_EMPLOYED = 'employed';
    const EMPLOYMENT_SELF_EMPLOYED = 'self_employed';
    const EMPLOYMENT_UNEMPLOYED = 'unemployed';
    const EMPLOYMENT_RETIRED = 'retired';

    // Relationships
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function loans(): BelongsToMany
    {
        return $this->belongsToMany(Loan::class, 'loan_guarantors')
                    ->withPivot(['guarantee_amount', 'status', 'approved_at'])
                    ->withTimestamps();
    }

    // Helper methods
    public function getAvailableGuaranteeAmount(): float
    {
        return $this->max_guarantee_amount - $this->current_guarantee_obligations;
    }

    public function canGuarantee(float $amount): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               $this->getAvailableGuaranteeAmount() >= $amount;
    }

    public function isEligible(): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               $this->employment_status !== self::EMPLOYMENT_UNEMPLOYED;
    }

    public function updateGuaranteeObligations(float $amount): bool
    {
        $this->current_guarantee_obligations += $amount;
        return $this->save();
    }

    public function reduceGuaranteeObligations(float $amount): bool
    {
        $this->current_guarantee_obligations = max(0, $this->current_guarantee_obligations - $amount);
        return $this->save();
    }

    public static function getEmploymentStatuses(): array
    {
        return [
            self::EMPLOYMENT_EMPLOYED => 'Employed',
            self::EMPLOYMENT_SELF_EMPLOYED => 'Self Employed',
            self::EMPLOYMENT_UNEMPLOYED => 'Unemployed',
            self::EMPLOYMENT_RETIRED => 'Retired',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_SUSPENDED => 'Suspended',
            self::STATUS_BLACKLISTED => 'Blacklisted',
        ];
    }
}
