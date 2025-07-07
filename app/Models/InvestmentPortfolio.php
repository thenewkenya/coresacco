<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class InvestmentPortfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'product_id',
        'certificate_number',
        'investment_amount',
        'units_purchased',
        'unit_price',
        'purchase_date',
        'maturity_date',
        'current_value',
        'accrued_interest',
        'dividend_earned',
        'status',
        'auto_renewal',
        'withdrawal_notice_date',
        'metadata'
    ];

    protected $casts = [
        'investment_amount' => 'decimal:2',
        'units_purchased' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'current_value' => 'decimal:2',
        'accrued_interest' => 'decimal:2',
        'dividend_earned' => 'decimal:2',
        'purchase_date' => 'datetime',
        'maturity_date' => 'datetime',
        'withdrawal_notice_date' => 'datetime',
        'auto_renewal' => 'boolean',
        'metadata' => 'array'
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_MATURED = 'matured';
    const STATUS_WITHDRAWN = 'withdrawn';
    const STATUS_RENEWED = 'renewed';
    const STATUS_PENDING_WITHDRAWAL = 'pending_withdrawal';

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(InvestmentProduct::class, 'product_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InvestmentTransaction::class, 'portfolio_id');
    }

    // Calculate current portfolio performance
    public function calculateReturns(): array
    {
        $currentValue = $this->getCurrentMarketValue();
        $investedAmount = $this->investment_amount;
        $absoluteReturn = $currentValue - $investedAmount;
        $percentageReturn = $investedAmount > 0 ? ($absoluteReturn / $investedAmount) * 100 : 0;
        
        // Calculate annualized return
        $daysSincePurchase = $this->purchase_date->diffInDays(now());
        $years = $daysSincePurchase / 365.25;
        $annualizedReturn = $years > 0 ? (pow(($currentValue / $investedAmount), (1 / $years)) - 1) * 100 : 0;

        return [
            'absolute_return' => round($absoluteReturn, 2),
            'percentage_return' => round($percentageReturn, 2),
            'annualized_return' => round($annualizedReturn, 2),
            'current_value' => $currentValue,
            'invested_amount' => $investedAmount,
            'total_dividends' => $this->dividend_earned,
            'total_interest' => $this->accrued_interest
        ];
    }

    public function getCurrentMarketValue(): float
    {
        // For fixed deposits and bonds, calculate based on accrued interest
        if (in_array($this->product->product_type, [
            InvestmentProduct::TYPE_FIXED_DEPOSIT,
            InvestmentProduct::TYPE_GOVERNMENT_BOND
        ])) {
            return $this->investment_amount + $this->calculateAccruedInterest();
        }

        // For market-based products, calculate based on current unit price
        // This would typically come from market data feeds
        $currentUnitPrice = $this->getCurrentUnitPrice();
        return $this->units_purchased * $currentUnitPrice;
    }

    public function calculateAccruedInterest(): float
    {
        if (!$this->product) {
            return 0;
        }

        $daysSincePurchase = $this->purchase_date->diffInDays(now());
        $dailyRate = ($this->product->interest_rate / 100) / 365;
        $accruedInterest = $this->investment_amount * $dailyRate * $daysSincePurchase;

        return round($accruedInterest, 2);
    }

    public function getCurrentUnitPrice(): float
    {
        // In a real implementation, this would fetch from market data
        // For now, simulate some market movement
        $volatility = match($this->product->risk_level) {
            InvestmentProduct::RISK_LOW => 0.02,
            InvestmentProduct::RISK_MEDIUM => 0.05,
            InvestmentProduct::RISK_HIGH => 0.10,
            default => 0.03
        };

        // Simulate market movement (in real app, this would come from external data)
        $randomFactor = 1 + (mt_rand(-100, 100) / 10000) * $volatility;
        return round($this->unit_price * $randomFactor, 4);
    }

    public function getDaysToMaturity(): int
    {
        if (!$this->maturity_date) {
            return 0;
        }

        return max(0, now()->diffInDays($this->maturity_date, false));
    }

    public function getMaturityStatus(): string
    {
        $daysToMaturity = $this->getDaysToMaturity();
        
        if ($daysToMaturity <= 0) {
            return 'matured';
        } elseif ($daysToMaturity <= 30) {
            return 'maturing_soon';
        } elseif ($daysToMaturity <= 90) {
            return 'maturing_this_quarter';
        }
        
        return 'active';
    }

    public function canWithdraw(): bool
    {
        // Check product liquidity rules
        if ($this->product->liquidity_type === InvestmentProduct::LIQUIDITY_LOW) {
            return $this->getDaysToMaturity() <= 0;
        }

        if ($this->product->liquidity_type === InvestmentProduct::LIQUIDITY_MEDIUM) {
            // Requires notice period (typically 30-90 days)
            return $this->withdrawal_notice_date && 
                   $this->withdrawal_notice_date->diffInDays(now()) >= 30;
        }

        // High liquidity - can withdraw anytime
        return true;
    }

    public function calculateWithdrawalAmount(): float
    {
        $currentValue = $this->getCurrentMarketValue();
        
        if ($this->getDaysToMaturity() > 0 && 
            $this->product->early_withdrawal_penalty > 0) {
            return $this->product->calculateEarlyWithdrawalAmount($currentValue);
        }

        return $currentValue;
    }

    public function processMaturity(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        $maturityValue = $this->getCurrentMarketValue();
        
        if ($this->auto_renewal && $this->product->status === InvestmentProduct::STATUS_ACTIVE) {
            // Auto-renew the investment
            $this->update([
                'investment_amount' => $maturityValue,
                'purchase_date' => now(),
                'maturity_date' => $this->product->term_months ? 
                    now()->addMonths($this->product->term_months) : null,
                'accrued_interest' => 0,
                'dividend_earned' => 0,
                'status' => self::STATUS_RENEWED
            ]);
            
            return true;
        }

        // Mark as matured for manual processing
        $this->update([
            'status' => self::STATUS_MATURED,
            'current_value' => $maturityValue
        ]);

        return true;
    }

    public function generateCertificateNumber(): string
    {
        $prefix = strtoupper(substr($this->product->product_type, 0, 2));
        $memberCode = str_pad($this->member_id, 4, '0', STR_PAD_LEFT);
        $sequence = str_pad($this->id, 6, '0', STR_PAD_LEFT);
        
        return $prefix . $memberCode . $sequence;
    }

    // Boot method to auto-generate certificate numbers
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($portfolio) {
            if (empty($portfolio->certificate_number)) {
                $portfolio->update([
                    'certificate_number' => $portfolio->generateCertificateNumber()
                ]);
            }
        });
    }
} 