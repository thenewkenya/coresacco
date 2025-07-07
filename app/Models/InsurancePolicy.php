<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class InsurancePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_number',
        'member_id',
        'product_id',
        'coverage_amount',
        'premium_amount',
        'premium_frequency',
        'policy_start_date',
        'policy_end_date',
        'next_premium_due_date',
        'status',
        'beneficiaries',
        'risk_assessment',
        'medical_exam_results',
        'property_inspection_results',
        'underwriting_notes',
        'agent_id',
        'commission_rate',
        'total_premiums_paid',
        'total_claims_paid',
        'last_premium_payment_date',
        'grace_period_end_date',
        'lapse_date',
        'reinstatement_date',
        'cancellation_reason',
        'metadata'
    ];

    protected $casts = [
        'coverage_amount' => 'decimal:2',
        'premium_amount' => 'decimal:2',
        'commission_rate' => 'decimal:4',
        'total_premiums_paid' => 'decimal:2',
        'total_claims_paid' => 'decimal:2',
        'policy_start_date' => 'date',
        'policy_end_date' => 'date',
        'next_premium_due_date' => 'date',
        'last_premium_payment_date' => 'date',
        'grace_period_end_date' => 'date',
        'lapse_date' => 'date',
        'reinstatement_date' => 'date',
        'beneficiaries' => 'json',
        'risk_assessment' => 'json',
        'medical_exam_results' => 'json',
        'property_inspection_results' => 'json',
        'underwriting_notes' => 'json',
        'metadata' => 'json'
    ];

    /**
     * Get the member who owns this policy
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    /**
     * Get the insurance product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(InsuranceProduct::class, 'product_id');
    }

    /**
     * Get the insurance agent
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get all premium payments for this policy
     */
    public function premiums(): HasMany
    {
        return $this->hasMany(InsurancePremium::class, 'policy_id');
    }

    /**
     * Get all claims for this policy
     */
    public function claims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class, 'policy_id');
    }

    /**
     * Check if policy is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if policy is in grace period
     */
    public function isInGracePeriod(): bool
    {
        return $this->grace_period_end_date && 
               Carbon::now()->lte($this->grace_period_end_date) && 
               $this->status === 'grace_period';
    }

    /**
     * Check if policy has lapsed
     */
    public function hasLapsed(): bool
    {
        return $this->status === 'lapsed';
    }

    /**
     * Check if premium is overdue
     */
    public function isPremiumOverdue(): bool
    {
        return $this->next_premium_due_date && 
               Carbon::now()->gt($this->next_premium_due_date) && 
               $this->status === 'active';
    }

    /**
     * Calculate next premium due date
     */
    public function calculateNextPremiumDueDate(): Carbon
    {
        $lastDueDate = $this->next_premium_due_date ?? $this->policy_start_date;
        
        return match($this->premium_frequency) {
            'monthly' => Carbon::parse($lastDueDate)->addMonth(),
            'quarterly' => Carbon::parse($lastDueDate)->addMonths(3),
            'semi_annually' => Carbon::parse($lastDueDate)->addMonths(6),
            'annually' => Carbon::parse($lastDueDate)->addYear(),
            default => Carbon::parse($lastDueDate)->addMonth()
        };
    }

    /**
     * Calculate grace period end date
     */
    public function calculateGracePeriodEndDate(): Carbon
    {
        $gracePeriodDays = $this->product->grace_period_days ?? 30;
        return Carbon::parse($this->next_premium_due_date)->addDays($gracePeriodDays);
    }

    /**
     * Get policy age in years
     */
    public function getPolicyAgeInYears(): float
    {
        return Carbon::parse($this->policy_start_date)->diffInYears(Carbon::now(), true);
    }

    /**
     * Get remaining coverage years
     */
    public function getRemainingCoverageYears(): float
    {
        if (!$this->policy_end_date) {
            return 0;
        }
        
        return Carbon::now()->diffInYears($this->policy_end_date, true);
    }

    /**
     * Calculate cash value (for permanent life insurance)
     */
    public function calculateCashValue(): float
    {
        if ($this->product->coverage_type !== 'whole_life' && 
            $this->product->coverage_type !== 'universal_life') {
            return 0;
        }

        $policyAge = $this->getPolicyAgeInYears();
        $totalPremiumsPaid = $this->total_premiums_paid ?? 0;
        
        // Simplified cash value calculation
        $cashValueRate = match($this->product->coverage_type) {
            'whole_life' => 0.60, // 60% of premiums paid after fees
            'universal_life' => 0.75, // 75% of premiums paid after fees
            default => 0
        };
        
        // Apply cash value only after the first year
        if ($policyAge < 1) {
            return 0;
        }
        
        return $totalPremiumsPaid * $cashValueRate;
    }

    /**
     * Check if policy is eligible for claims
     */
    public function isEligibleForClaims(): bool
    {
        $waitingPeriod = $this->product->waiting_period_days ?? 0;
        $policyAge = Carbon::parse($this->policy_start_date)->diffInDays(Carbon::now());
        
        return $this->isActive() && $policyAge >= $waitingPeriod;
    }

    /**
     * Get policy status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'active' => 'Active',
            'grace_period' => 'Grace Period',
            'lapsed' => 'Lapsed',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
            'claim_paid' => 'Claim Paid',
            'suspended' => 'Suspended'
        ];
    }
} 