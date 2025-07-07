<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsuranceProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'insurance_type',
        'coverage_type',
        'min_coverage_amount',
        'max_coverage_amount',
        'min_age',
        'max_age',
        'base_premium_rate',
        'risk_factors',
        'coverage_benefits',
        'exclusions',
        'terms_conditions',
        'premium_frequency',
        'grace_period_days',
        'waiting_period_days',
        'claim_settlement_days',
        'renewal_terms',
        'status',
        'requires_medical_exam',
        'requires_property_inspection',
        'commission_rate',
        'metadata'
    ];

    protected $casts = [
        'min_coverage_amount' => 'decimal:2',
        'max_coverage_amount' => 'decimal:2',
        'base_premium_rate' => 'decimal:4',
        'commission_rate' => 'decimal:4',
        'risk_factors' => 'json',
        'coverage_benefits' => 'json',
        'exclusions' => 'json',
        'terms_conditions' => 'json',
        'renewal_terms' => 'json',
        'requires_medical_exam' => 'boolean',
        'requires_property_inspection' => 'boolean',
        'metadata' => 'json'
    ];

    /**
     * Get the insurance policies for this product
     */
    public function policies(): HasMany
    {
        return $this->hasMany(InsurancePolicy::class, 'product_id');
    }

    /**
     * Get the insurance claims for this product
     */
    public function claims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class, 'product_id');
    }

    /**
     * Check if product is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if age is within coverage range
     */
    public function isAgeEligible(int $age): bool
    {
        return $age >= $this->min_age && $age <= $this->max_age;
    }

    /**
     * Calculate premium based on coverage amount and risk factors
     */
    public function calculatePremium(float $coverageAmount, array $riskFactors = []): float
    {
        $basePremium = $coverageAmount * ($this->base_premium_rate / 100);
        
        // Apply risk factor multipliers
        $riskMultiplier = 1.0;
        foreach ($riskFactors as $factor => $value) {
            if (isset($this->risk_factors[$factor])) {
                $riskMultiplier *= $this->risk_factors[$factor][$value] ?? 1.0;
            }
        }
        
        return $basePremium * $riskMultiplier;
    }

    /**
     * Get premium frequency options
     */
    public static function getPremiumFrequencies(): array
    {
        return [
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'semi_annually' => 'Semi-Annually',
            'annually' => 'Annually'
        ];
    }

    /**
     * Get insurance types
     */
    public static function getInsuranceTypes(): array
    {
        return [
            'life' => 'Life Insurance',
            'health' => 'Health Insurance',
            'property' => 'Property Insurance',
            'crop' => 'Crop Insurance',
            'micro' => 'Microinsurance',
            'travel' => 'Travel Insurance',
            'business' => 'Business Insurance'
        ];
    }

    /**
     * Get coverage types
     */
    public static function getCoverageTypes(): array
    {
        return [
            'term_life' => 'Term Life',
            'whole_life' => 'Whole Life',
            'universal_life' => 'Universal Life',
            'medical' => 'Medical',
            'dental' => 'Dental',
            'vision' => 'Vision',
            'home' => 'Home',
            'auto' => 'Auto',
            'business_property' => 'Business Property',
            'crop_yield' => 'Crop Yield',
            'crop_revenue' => 'Crop Revenue',
            'livestock' => 'Livestock',
            'micro_life' => 'Micro Life',
            'micro_health' => 'Micro Health',
            'micro_property' => 'Micro Property'
        ];
    }
} 