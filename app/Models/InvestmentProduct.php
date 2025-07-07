<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvestmentProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'product_type',
        'minimum_investment',
        'maximum_investment',
        'interest_rate',
        'dividend_rate',
        'risk_level',
        'term_months',
        'compounding_frequency',
        'liquidity_type',
        'early_withdrawal_penalty',
        'management_fee',
        'status',
        'maturity_benefits',
        'terms_conditions'
    ];

    protected $casts = [
        'minimum_investment' => 'decimal:2',
        'maximum_investment' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'dividend_rate' => 'decimal:2',
        'early_withdrawal_penalty' => 'decimal:2',
        'management_fee' => 'decimal:2',
        'maturity_benefits' => 'array',
        'terms_conditions' => 'array'
    ];

    // Product types
    const TYPE_FIXED_DEPOSIT = 'fixed_deposit';
    const TYPE_MONEY_MARKET = 'money_market';
    const TYPE_GOVERNMENT_BOND = 'government_bond';
    const TYPE_EQUITY_FUND = 'equity_fund';
    const TYPE_BALANCED_FUND = 'balanced_fund';
    const TYPE_RETIREMENT_FUND = 'retirement_fund';

    // Risk levels
    const RISK_LOW = 'low';
    const RISK_MEDIUM = 'medium';
    const RISK_HIGH = 'high';

    // Liquidity types
    const LIQUIDITY_HIGH = 'high'; // Can withdraw anytime
    const LIQUIDITY_MEDIUM = 'medium'; // Can withdraw with notice
    const LIQUIDITY_LOW = 'low'; // Fixed term

    // Status
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    public function portfolios(): HasMany
    {
        return $this->hasMany(InvestmentPortfolio::class, 'product_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InvestmentTransaction::class, 'product_id');
    }

    // Helper methods
    public function isEligibleAmount(float $amount): bool
    {
        return $amount >= $this->minimum_investment && 
               ($this->maximum_investment === null || $amount <= $this->maximum_investment);
    }

    public function calculateMaturityValue(float $principalAmount, int $termMonths = null): float
    {
        $term = $termMonths ?? $this->term_months;
        $rate = $this->interest_rate / 100;
        
        // Compound interest calculation
        $compoundingPerYear = match($this->compounding_frequency) {
            'annually' => 1,
            'semi_annually' => 2,
            'quarterly' => 4,
            'monthly' => 12,
            'daily' => 365,
            default => 12
        };
        
        $years = $term / 12;
        $maturityValue = $principalAmount * pow((1 + $rate / $compoundingPerYear), $compoundingPerYear * $years);
        
        return round($maturityValue, 2);
    }

    public function calculateEarlyWithdrawalAmount(float $currentValue): float
    {
        $penalty = ($this->early_withdrawal_penalty / 100) * $currentValue;
        return max(0, $currentValue - $penalty);
    }

    public function getExpectedMonthlyReturn(float $investmentAmount): float
    {
        return ($investmentAmount * $this->interest_rate / 100) / 12;
    }

    public static function getAvailableProducts(): array
    {
        return [
            self::TYPE_FIXED_DEPOSIT => 'Fixed Deposit',
            self::TYPE_MONEY_MARKET => 'Money Market Fund',
            self::TYPE_GOVERNMENT_BOND => 'Government Bonds',
            self::TYPE_EQUITY_FUND => 'Equity Fund',
            self::TYPE_BALANCED_FUND => 'Balanced Fund',
            self::TYPE_RETIREMENT_FUND => 'Retirement Fund'
        ];
    }

    public static function getRiskLevels(): array
    {
        return [
            self::RISK_LOW => 'Low Risk',
            self::RISK_MEDIUM => 'Medium Risk',
            self::RISK_HIGH => 'High Risk'
        ];
    }
} 