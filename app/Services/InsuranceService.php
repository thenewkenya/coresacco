<?php

namespace App\Services;

use App\Models\InsuranceProduct;
use App\Models\InsurancePolicy;
use App\Models\InsuranceClaim;
use App\Models\InsurancePremium;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InsuranceService
{
    /**
     * Create a new insurance policy
     */
    public function createPolicy(
        User $member,
        InsuranceProduct $product,
        float $coverageAmount,
        array $beneficiaries = [],
        array $riskFactors = [],
        array $metadata = []
    ): InsurancePolicy {
        
        // Validate eligibility
        $this->validatePolicyEligibility($member, $product, $coverageAmount);
        
        // Calculate premium
        $premiumAmount = $product->calculatePremium($coverageAmount, $riskFactors);
        
        return DB::transaction(function () use ($member, $product, $coverageAmount, $premiumAmount, $beneficiaries, $riskFactors, $metadata) {
            // Create the policy
            $policy = InsurancePolicy::create([
                'policy_number' => $this->generatePolicyNumber($product),
                'member_id' => $member->id,
                'product_id' => $product->id,
                'coverage_amount' => $coverageAmount,
                'premium_amount' => $premiumAmount,
                'premium_frequency' => $product->premium_frequency,
                'policy_start_date' => Carbon::now(),
                'policy_end_date' => $this->calculatePolicyEndDate($product),
                'next_premium_due_date' => $this->calculateNextPremiumDueDate($product->premium_frequency),
                'status' => 'pending',
                'beneficiaries' => $beneficiaries,
                'risk_assessment' => $riskFactors,
                'commission_rate' => $product->commission_rate,
                'metadata' => $metadata
            ]);
            
            // Create initial premium
            $this->createPremium($policy, $premiumAmount);
            
            return $policy;
        });
    }
    
    /**
     * Validate policy eligibility
     */
    private function validatePolicyEligibility(User $member, InsuranceProduct $product, float $coverageAmount): void
    {
        if (!$product->isActive()) {
            throw new \Exception("This insurance product is not currently available");
        }
        
        if ($coverageAmount < $product->min_coverage_amount) {
            throw new \Exception("Minimum coverage amount is " . number_format($product->min_coverage_amount, 2));
        }
        
        if ($coverageAmount > $product->max_coverage_amount) {
            throw new \Exception("Maximum coverage amount is " . number_format($product->max_coverage_amount, 2));
        }
        
        // Check age eligibility
        $memberAge = Carbon::parse($member->date_of_birth)->age ?? 25;
        if (!$product->isAgeEligible($memberAge)) {
            throw new \Exception("Age eligibility is between {$product->min_age} and {$product->max_age} years");
        }
    }
    
    /**
     * Process premium payment
     */
    public function processPremiumPayment(
        InsurancePremium $premium,
        float $paymentAmount,
        string $paymentMethod,
        string $paymentReference,
        User $processor = null
    ): void {
        
        if ($premium->isPaid()) {
            throw new \Exception("Premium has already been paid");
        }
        
        $totalDue = $premium->getTotalAmountDue();
        
        if ($paymentAmount < $totalDue) {
            throw new \Exception("Payment amount is insufficient. Total due: " . number_format($totalDue, 2));
        }
        
        DB::transaction(function () use ($premium, $paymentAmount, $paymentMethod, $paymentReference, $processor) {
            // Update premium
            $premium->update([
                'payment_date' => Carbon::now(),
                'payment_method' => $paymentMethod,
                'payment_reference' => $paymentReference,
                'status' => 'paid',
                'processed_by' => $processor?->id
            ]);
            
            // Update policy
            $policy = $premium->policy;
            $policy->update([
                'total_premiums_paid' => $policy->total_premiums_paid + $paymentAmount,
                'last_premium_payment_date' => Carbon::now(),
                'next_premium_due_date' => $policy->calculateNextPremiumDueDate(),
                'status' => 'active'
            ]);
            
            // Create next premium if policy is ongoing
            if ($policy->status === 'active' && !$this->isPolicyExpired($policy)) {
                $this->createPremium($policy, $policy->premium_amount);
            }
        });
    }
    
    /**
     * Create insurance claim
     */
    public function createClaim(
        InsurancePolicy $policy,
        string $claimType,
        Carbon $incidentDate,
        float $claimedAmount,
        string $incidentDescription,
        array $supportingDocuments = [],
        array $metadata = []
    ): InsuranceClaim {
        
        // Validate claim eligibility
        $this->validateClaimEligibility($policy, $incidentDate, $claimedAmount);
        
        return InsuranceClaim::create([
            'claim_number' => $this->generateClaimNumber(),
            'policy_id' => $policy->id,
            'product_id' => $policy->product_id,
            'member_id' => $policy->member_id,
            'claim_type' => $claimType,
            'incident_date' => $incidentDate,
            'claim_date' => Carbon::now(),
            'claimed_amount' => $claimedAmount,
            'status' => 'pending',
            'incident_description' => $incidentDescription,
            'supporting_documents' => $supportingDocuments,
            'metadata' => $metadata
        ]);
    }
    
    /**
     * Process insurance claim
     */
    public function processClaim(
        InsuranceClaim $claim,
        string $decision,
        float $approvedAmount = null,
        string $notes = null,
        User $processor = null
    ): void {
        
        if (!in_array($decision, ['approved', 'rejected'])) {
            throw new \Exception("Invalid claim decision. Must be 'approved' or 'rejected'");
        }
        
        DB::transaction(function () use ($claim, $decision, $approvedAmount, $notes, $processor) {
            $updateData = [
                'status' => $decision,
                'processed_by' => $processor?->id,
                'processed_date' => Carbon::now()
            ];
            
            if ($decision === 'approved') {
                $updateData['approved_amount'] = $approvedAmount ?? $claim->claimed_amount;
                $updateData['approved_by'] = $processor?->id;
            } else {
                $updateData['rejection_reason'] = $notes;
            }
            
            $claim->update($updateData);
            
            // Update policy total claims if approved
            if ($decision === 'approved') {
                $policy = $claim->policy;
                $policy->increment('total_claims_paid', $updateData['approved_amount']);
            }
        });
    }
    
    /**
     * Pay approved claim
     */
    public function payClaim(
        InsuranceClaim $claim,
        string $paymentMethod,
        string $paymentReference,
        User $processor = null
    ): void {
        
        if (!$claim->isApproved()) {
            throw new \Exception("Claim must be approved before payment");
        }
        
        if ($claim->isPaid()) {
            throw new \Exception("Claim has already been paid");
        }
        
        $claim->update([
            'status' => 'paid',
            'payment_date' => Carbon::now(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
            'processed_by' => $processor?->id
        ]);
    }
    
    /**
     * Get policy summary for a member
     */
    public function getPolicySummary(User $member): array
    {
        $policies = InsurancePolicy::where('member_id', $member->id)
            ->with(['product', 'claims', 'premiums'])
            ->get();
        
        $summary = [
            'total_policies' => $policies->count(),
            'active_policies' => $policies->where('status', 'active')->count(),
            'total_coverage' => $policies->sum('coverage_amount'),
            'total_premiums_paid' => $policies->sum('total_premiums_paid'),
            'total_claims_paid' => $policies->sum('total_claims_paid'),
            'pending_claims' => 0,
            'overdue_premiums' => 0,
            'policies_by_type' => [],
            'recent_claims' => [],
            'upcoming_premiums' => []
        ];
        
        foreach ($policies as $policy) {
            $insuranceType = $policy->product->insurance_type;
            if (!isset($summary['policies_by_type'][$insuranceType])) {
                $summary['policies_by_type'][$insuranceType] = 0;
            }
            $summary['policies_by_type'][$insuranceType]++;
            
            // Count pending claims
            $summary['pending_claims'] += $policy->claims->whereIn('status', ['pending', 'investigating'])->count();
            
            // Count overdue premiums
            $summary['overdue_premiums'] += $policy->premiums->where('status', 'overdue')->count();
        }
        
        return $summary;
    }
    
    /**
     * Calculate policy cash value
     */
    public function calculateCashValue(InsurancePolicy $policy): float
    {
        return $policy->calculateCashValue();
    }
    
    /**
     * Process policy lapse
     */
    public function lapsePolicies(): int
    {
        $overdueThreshold = Carbon::now()->subDays(30); // 30 days past grace period
        
        $policiesToLapse = InsurancePolicy::where('status', 'grace_period')
            ->where('grace_period_end_date', '<', Carbon::now())
            ->get();
        
        $lapsedCount = 0;
        
        foreach ($policiesToLapse as $policy) {
            $policy->update([
                'status' => 'lapsed',
                'lapse_date' => Carbon::now()
            ]);
            $lapsedCount++;
        }
        
        return $lapsedCount;
    }
    
    /**
     * Reinstate lapsed policy
     */
    public function reinstatePolicy(InsurancePolicy $policy, array $conditions = []): void
    {
        if (!$policy->hasLapsed()) {
            throw new \Exception("Policy is not in lapsed status");
        }
        
        DB::transaction(function () use ($policy, $conditions) {
            $policy->update([
                'status' => 'active',
                'reinstatement_date' => Carbon::now(),
                'next_premium_due_date' => $policy->calculateNextPremiumDueDate(),
                'metadata' => array_merge($policy->metadata ?? [], [
                    'reinstatement_conditions' => $conditions
                ])
            ]);
            
            // Create new premium
            $this->createPremium($policy, $policy->premium_amount);
        });
    }
    
    /**
     * Private helper methods
     */
    private function generatePolicyNumber(InsuranceProduct $product): string
    {
        $prefix = strtoupper(substr($product->insurance_type, 0, 3));
        $sequence = str_pad(InsurancePolicy::count() + 1, 8, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$sequence}";
    }
    
    private function generateClaimNumber(): string
    {
        return 'CLM-' . strtoupper(Str::random(6)) . '-' . time();
    }
    
    private function calculatePolicyEndDate(InsuranceProduct $product): ?Carbon
    {
        // Term life insurance typically has end dates
        if ($product->coverage_type === 'term_life') {
            return Carbon::now()->addYears(10); // Default 10 years
        }
        
        // Crop insurance typically has seasonal end dates
        if ($product->insurance_type === 'crop') {
            return Carbon::now()->addMonths(12); // One year
        }
        
        // Other products might be permanent
        return null;
    }
    
    private function calculateNextPremiumDueDate(string $frequency): Carbon
    {
        return match($frequency) {
            'monthly' => Carbon::now()->addMonth(),
            'quarterly' => Carbon::now()->addMonths(3),
            'semi_annually' => Carbon::now()->addMonths(6),
            'annually' => Carbon::now()->addYear(),
            default => Carbon::now()->addMonth()
        };
    }
    
    private function createPremium(InsurancePolicy $policy, float $amount): InsurancePremium
    {
        return InsurancePremium::create([
            'policy_id' => $policy->id,
            'member_id' => $policy->member_id,
            'premium_amount' => $amount,
            'commission_amount' => $amount * ($policy->commission_rate / 100),
            'due_date' => $policy->next_premium_due_date,
            'grace_period_end_date' => $policy->calculateGracePeriodEndDate(),
            'status' => 'pending'
        ]);
    }
    
    private function validateClaimEligibility(InsurancePolicy $policy, Carbon $incidentDate, float $claimedAmount): void
    {
        if (!$policy->isEligibleForClaims()) {
            throw new \Exception("Policy is not eligible for claims at this time");
        }
        
        if ($claimedAmount > $policy->coverage_amount) {
            throw new \Exception("Claimed amount exceeds coverage amount");
        }
        
        if ($incidentDate->gt(Carbon::now())) {
            throw new \Exception("Incident date cannot be in the future");
        }
    }
    
    private function isPolicyExpired(InsurancePolicy $policy): bool
    {
        return $policy->policy_end_date && Carbon::now()->gt($policy->policy_end_date);
    }
} 