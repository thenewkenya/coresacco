<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class InsuranceClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_number',
        'policy_id',
        'product_id',
        'member_id',
        'claim_type',
        'incident_date',
        'claim_date',
        'claimed_amount',
        'approved_amount',
        'deductible_amount',
        'status',
        'incident_description',
        'supporting_documents',
        'medical_reports',
        'police_reports',
        'witness_statements',
        'adjuster_notes',
        'settlement_notes',
        'rejection_reason',
        'processed_by',
        'approved_by',
        'processed_date',
        'payment_date',
        'payment_method',
        'payment_reference',
        'reopened_date',
        'reopened_reason',
        'fraud_investigation',
        'metadata'
    ];

    protected $casts = [
        'incident_date' => 'date',
        'claim_date' => 'date',
        'processed_date' => 'date',
        'payment_date' => 'date',
        'reopened_date' => 'date',
        'claimed_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'deductible_amount' => 'decimal:2',
        'supporting_documents' => 'json',
        'medical_reports' => 'json',
        'police_reports' => 'json',
        'witness_statements' => 'json',
        'adjuster_notes' => 'json',
        'settlement_notes' => 'json',
        'fraud_investigation' => 'json',
        'metadata' => 'json'
    ];

    /**
     * Get the policy associated with this claim
     */
    public function policy(): BelongsTo
    {
        return $this->belongsTo(InsurancePolicy::class, 'policy_id');
    }

    /**
     * Get the insurance product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(InsuranceProduct::class, 'product_id');
    }

    /**
     * Get the member who filed the claim
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    /**
     * Get the user who processed the claim
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the user who approved the claim
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if claim is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if claim is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if claim is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if claim is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if claim is under investigation
     */
    public function isUnderInvestigation(): bool
    {
        return $this->status === 'investigating';
    }

    /**
     * Calculate net payout amount
     */
    public function getNetPayoutAmount(): float
    {
        return ($this->approved_amount ?? 0) - ($this->deductible_amount ?? 0);
    }

    /**
     * Get claim processing days
     */
    public function getProcessingDays(): int
    {
        if (!$this->processed_date) {
            return Carbon::parse($this->claim_date)->diffInDays(Carbon::now());
        }
        
        return Carbon::parse($this->claim_date)->diffInDays($this->processed_date);
    }

    /**
     * Check if claim is within settlement timeline
     */
    public function isWithinSettlementTimeline(): bool
    {
        $settlementDays = $this->product->claim_settlement_days ?? 30;
        return $this->getProcessingDays() <= $settlementDays;
    }

    /**
     * Check if claim is overdue
     */
    public function isOverdue(): bool
    {
        if ($this->isPaid() || $this->isRejected()) {
            return false;
        }
        
        $settlementDays = $this->product->claim_settlement_days ?? 30;
        return $this->getProcessingDays() > $settlementDays;
    }

    /**
     * Calculate settlement delay days
     */
    public function getSettlementDelayDays(): int
    {
        $settlementDays = $this->product->claim_settlement_days ?? 30;
        $processingDays = $this->getProcessingDays();
        
        return max(0, $processingDays - $settlementDays);
    }

    /**
     * Check if incident is within policy coverage period
     */
    public function isIncidentWithinCoverage(): bool
    {
        $policy = $this->policy;
        $incidentDate = Carbon::parse($this->incident_date);
        
        $withinStart = $incidentDate->gte($policy->policy_start_date);
        $withinEnd = !$policy->policy_end_date || $incidentDate->lte($policy->policy_end_date);
        
        return $withinStart && $withinEnd;
    }

    /**
     * Check if claim is within reporting period
     */
    public function isWithinReportingPeriod(int $maxReportingDays = 30): bool
    {
        $reportingDays = Carbon::parse($this->incident_date)->diffInDays($this->claim_date);
        return $reportingDays <= $maxReportingDays;
    }

    /**
     * Get claim types
     */
    public static function getClaimTypes(): array
    {
        return [
            'death' => 'Death Benefit',
            'disability' => 'Disability',
            'medical' => 'Medical Treatment',
            'dental' => 'Dental Treatment',
            'vision' => 'Vision Care',
            'hospital' => 'Hospitalization',
            'surgery' => 'Surgery',
            'prescription' => 'Prescription Drugs',
            'fire' => 'Fire Damage',
            'theft' => 'Theft',
            'flood' => 'Flood Damage',
            'earthquake' => 'Earthquake',
            'accident' => 'Accident',
            'liability' => 'Liability',
            'crop_loss' => 'Crop Loss',
            'livestock_death' => 'Livestock Death',
            'equipment_damage' => 'Equipment Damage',
            'business_interruption' => 'Business Interruption',
            'other' => 'Other'
        ];
    }

    /**
     * Get claim status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending Review',
            'investigating' => 'Under Investigation',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'paid' => 'Paid',
            'reopened' => 'Reopened',
            'appealed' => 'Under Appeal',
            'settled' => 'Settled',
            'fraud_suspected' => 'Fraud Suspected'
        ];
    }
} 