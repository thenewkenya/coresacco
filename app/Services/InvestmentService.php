<?php

namespace App\Services;

use App\Models\InvestmentProduct;
use App\Models\InvestmentPortfolio;
use App\Models\InvestmentTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvestmentService
{
    /**
     * Purchase an investment product
     */
    public function purchaseInvestment(User $member, InvestmentProduct $product, float $amount, array $metadata = []): InvestmentPortfolio
    {
        if ($amount < $product->minimum_investment) {
            throw new \Exception("Minimum investment amount is " . number_format($product->minimum_investment, 2));
        }

        if ($product->maximum_investment && $amount > $product->maximum_investment) {
            throw new \Exception("Maximum investment amount is " . number_format($product->maximum_investment, 2));
        }

        if ($product->status !== 'active') {
            throw new \Exception("This investment product is not currently available");
        }

        return DB::transaction(function () use ($member, $product, $amount, $metadata) {
            // Calculate units and maturity date
            $units = $amount / $product->unit_price ?? 1;
            $maturityDate = $product->term_months ? 
                Carbon::now()->addMonths($product->term_months) : null;

            // Create investment portfolio
            $portfolio = InvestmentPortfolio::create([
                'member_id' => $member->id,
                'product_id' => $product->id,
                'certificate_number' => $this->generateCertificateNumber($product),
                'investment_amount' => $amount,
                'units_purchased' => $units,
                'unit_price' => $product->unit_price ?? 1,
                'purchase_date' => Carbon::now(),
                'maturity_date' => $maturityDate,
                'current_value' => $amount,
                'status' => 'active',
                'metadata' => $metadata
            ]);

            // Create purchase transaction
            InvestmentTransaction::create([
                'member_id' => $member->id,
                'portfolio_id' => $portfolio->id,
                'product_id' => $product->id,
                'transaction_type' => 'purchase',
                'amount' => $amount,
                'units' => $units,
                'unit_price' => $product->unit_price ?? 1,
                'description' => "Purchase of {$product->name}",
                'reference_number' => $this->generateReferenceNumber(),
                'status' => 'completed',
                'processed_at' => Carbon::now()
            ]);

            return $portfolio;
        });
    }

    /**
     * Calculate investment returns
     */
    public function calculateReturns(InvestmentPortfolio $portfolio): array
    {
        $product = $portfolio->product;
        $daysSincePurchase = Carbon::parse($portfolio->purchase_date)->diffInDays(Carbon::now());
        $yearsSincePurchase = $daysSincePurchase / 365;

        $returns = [
            'principal' => $portfolio->investment_amount,
            'interest_earned' => 0,
            'dividend_earned' => 0,
            'current_value' => $portfolio->investment_amount,
            'total_return' => 0,
            'return_percentage' => 0,
            'days_invested' => $daysSincePurchase,
            'annualized_return' => 0
        ];

        // Calculate interest based on product type and compounding frequency
        if ($product->interest_rate > 0) {
            $interest = $this->calculateCompoundInterest(
                $portfolio->investment_amount,
                $product->interest_rate / 100,
                $product->compounding_frequency,
                $yearsSincePurchase
            );
            $returns['interest_earned'] = $interest;
        }

        // Calculate dividends (simplified - would be based on actual dividend declarations)
        if ($product->dividend_rate > 0) {
            $dividends = $portfolio->investment_amount * ($product->dividend_rate / 100) * $yearsSincePurchase;
            $returns['dividend_earned'] = $dividends;
        }

        $returns['current_value'] = $portfolio->investment_amount + $returns['interest_earned'] + $returns['dividend_earned'];
        $returns['total_return'] = $returns['current_value'] - $portfolio->investment_amount;
        $returns['return_percentage'] = ($returns['total_return'] / $portfolio->investment_amount) * 100;
        $returns['annualized_return'] = $yearsSincePurchase > 0 ? 
            ($returns['return_percentage'] / $yearsSincePurchase) : 0;

        return $returns;
    }

    /**
     * Process investment withdrawal
     */
    public function processWithdrawal(InvestmentPortfolio $portfolio, float $amount = null, string $reason = null): InvestmentTransaction
    {
        if ($portfolio->status !== 'active') {
            throw new \Exception("Investment is not in active status");
        }

        $product = $portfolio->product;
        $returns = $this->calculateReturns($portfolio);
        $withdrawalAmount = $amount ?? $returns['current_value'];

        if ($amount && $amount > $returns['current_value']) {
            throw new \Exception("Withdrawal amount exceeds current investment value");
        }

        return DB::transaction(function () use ($portfolio, $withdrawalAmount, $reason, $product, $returns) {
            $penalty = 0;
            $isEarlyWithdrawal = false;

            // Check for early withdrawal penalty
            if ($portfolio->maturity_date && Carbon::now()->lt($portfolio->maturity_date)) {
                $isEarlyWithdrawal = true;
                $penalty = $withdrawalAmount * ($product->early_withdrawal_penalty / 100);
            }

            $netAmount = $withdrawalAmount - $penalty;

            // Create withdrawal transaction
            $transaction = InvestmentTransaction::create([
                'member_id' => $portfolio->member_id,
                'portfolio_id' => $portfolio->id,
                'product_id' => $product->id,
                'transaction_type' => $withdrawalAmount >= $returns['current_value'] ? 'withdrawal' : 'partial_withdrawal',
                'amount' => $netAmount,
                'units' => $withdrawalAmount >= $returns['current_value'] ? $portfolio->units_purchased : 0,
                'unit_price' => $portfolio->unit_price,
                'description' => "Withdrawal from {$product->name}" . ($reason ? " - {$reason}" : ''),
                'reference_number' => $this->generateReferenceNumber(),
                'status' => 'pending',
                'metadata' => [
                    'gross_amount' => $withdrawalAmount,
                    'penalty' => $penalty,
                    'net_amount' => $netAmount,
                    'early_withdrawal' => $isEarlyWithdrawal,
                    'reason' => $reason
                ]
            ]);

            // Update portfolio status
            if ($withdrawalAmount >= $returns['current_value']) {
                $portfolio->update(['status' => 'pending_withdrawal']);
            }

            return $transaction;
        });
    }

    /**
     * Calculate compound interest
     */
    private function calculateCompoundInterest(float $principal, float $rate, string $frequency, float $years): float
    {
        $compoundingPeriods = [
            'daily' => 365,
            'monthly' => 12,
            'quarterly' => 4,
            'semi_annually' => 2,
            'annually' => 1
        ];

        $n = $compoundingPeriods[$frequency] ?? 12;
        $amount = $principal * pow((1 + $rate / $n), $n * $years);
        
        return $amount - $principal;
    }

    /**
     * Generate certificate number
     */
    private function generateCertificateNumber(InvestmentProduct $product): string
    {
        $prefix = strtoupper(substr($product->product_type, 0, 2));
        $sequence = str_pad(InvestmentPortfolio::count() + 1, 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$sequence}";
    }

    /**
     * Generate reference number
     */
    private function generateReferenceNumber(): string
    {
        return 'INV-' . strtoupper(Str::random(8)) . '-' . time();
    }

    /**
     * Get investment portfolio summary for a member
     */
    public function getPortfolioSummary(User $member): array
    {
        $portfolios = InvestmentPortfolio::where('member_id', $member->id)
            ->with('product')
            ->get();

        $summary = [
            'total_investments' => 0,
            'current_value' => 0,
            'total_returns' => 0,
            'active_investments' => 0,
            'products' => []
        ];

        foreach ($portfolios as $portfolio) {
            $returns = $this->calculateReturns($portfolio);
            
            $summary['total_investments'] += $portfolio->investment_amount;
            $summary['current_value'] += $returns['current_value'];
            $summary['total_returns'] += $returns['total_return'];
            
            if ($portfolio->status === 'active') {
                $summary['active_investments']++;
            }

            $summary['products'][] = [
                'portfolio' => $portfolio,
                'returns' => $returns
            ];
        }

        return $summary;
    }

    /**
     * Process dividend distribution
     */
    public function distributeDividends(InvestmentProduct $product, float $dividendRate, Carbon $declarationDate): int
    {
        $activePortfolios = InvestmentPortfolio::where('product_id', $product->id)
            ->where('status', 'active')
            ->get();

        $distributedCount = 0;

        foreach ($activePortfolios as $portfolio) {
            $dividendAmount = $portfolio->investment_amount * ($dividendRate / 100);
            
            // Create dividend transaction
            InvestmentTransaction::create([
                'member_id' => $portfolio->member_id,
                'portfolio_id' => $portfolio->id,
                'product_id' => $product->id,
                'transaction_type' => 'dividend',
                'amount' => $dividendAmount,
                'units' => 0,
                'unit_price' => $portfolio->unit_price,
                'description' => "Dividend payment for {$product->name}",
                'reference_number' => $this->generateReferenceNumber(),
                'status' => 'completed',
                'processed_at' => $declarationDate,
                'metadata' => [
                    'dividend_rate' => $dividendRate,
                    'declaration_date' => $declarationDate->toDateString()
                ]
            ]);

            // Update portfolio dividend earned
            $portfolio->increment('dividend_earned', $dividendAmount);
            $distributedCount++;
        }

        return $distributedCount;
    }
} 