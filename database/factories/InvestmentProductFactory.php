<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvestmentProduct>
 */
class InvestmentProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productTypes = ['fixed_deposit', 'money_market', 'government_bond', 'equity_fund', 'balanced_fund', 'retirement_fund'];
        $productType = $this->faker->randomElement($productTypes);
        
        $riskLevels = ['low', 'medium', 'high'];
        $riskLevel = $this->faker->randomElement($riskLevels);
        
        $liquidityTypes = ['high', 'medium', 'low'];
        $liquidityType = $this->faker->randomElement($liquidityTypes);
        
        $compoundingFrequencies = ['daily', 'monthly', 'quarterly', 'semi_annually', 'annually'];
        $compoundingFrequency = $this->faker->randomElement($compoundingFrequencies);
        
        // Set realistic interest rates based on product type
        $interestRate = match($productType) {
            'fixed_deposit' => $this->faker->randomFloat(2, 5, 12),
            'money_market' => $this->faker->randomFloat(2, 3, 8),
            'government_bond' => $this->faker->randomFloat(2, 8, 15),
            'equity_fund' => $this->faker->randomFloat(2, 10, 25),
            'balanced_fund' => $this->faker->randomFloat(2, 8, 18),
            'retirement_fund' => $this->faker->randomFloat(2, 12, 20),
            default => $this->faker->randomFloat(2, 5, 15)
        };
        
        $dividendRate = $productType === 'equity_fund' || $productType === 'balanced_fund' ? 
            $this->faker->randomFloat(2, 3, 8) : 0;
        
        $minInvestment = $this->faker->randomElement([1000, 5000, 10000, 25000, 50000, 100000]);
        $maxInvestment = $minInvestment * $this->faker->numberBetween(10, 50);
        
        $termMonths = $productType === 'fixed_deposit' ? 
            $this->faker->randomElement([3, 6, 12, 24, 36, 48]) : 
            ($productType === 'retirement_fund' ? 
                $this->faker->numberBetween(60, 120) : 
                null);
        
        $names = [
            'fixed_deposit' => 'Fixed Deposit Plus',
            'money_market' => 'Money Market Fund',
            'government_bond' => 'Government Bond Fund',
            'equity_fund' => 'Equity Growth Fund',
            'balanced_fund' => 'Balanced Growth Fund',
            'retirement_fund' => 'Retirement Savings Plan'
        ];
        
        $descriptions = [
            'fixed_deposit' => 'Secure fixed deposit with guaranteed returns and flexible terms.',
            'money_market' => 'High liquidity money market fund with competitive returns.',
            'government_bond' => 'Government-backed bond fund offering stable returns.',
            'equity_fund' => 'Equity-focused fund targeting capital appreciation.',
            'balanced_fund' => 'Balanced portfolio of stocks and bonds for moderate risk.',
            'retirement_fund' => 'Long-term retirement savings with tax advantages.'
        ];
        
        return [
            'name' => $names[$productType] . ' ' . $this->faker->randomElement(['Premium', 'Classic', 'Elite', 'Pro']),
            'description' => $descriptions[$productType] . ' ' . $this->faker->sentence(8),
            'product_type' => $productType,
            'minimum_investment' => $minInvestment,
            'maximum_investment' => $maxInvestment,
            'interest_rate' => $interestRate,
            'dividend_rate' => $dividendRate,
            'risk_level' => $riskLevel,
            'term_months' => $termMonths,
            'compounding_frequency' => $compoundingFrequency,
            'liquidity_type' => $liquidityType,
            'early_withdrawal_penalty' => $this->faker->randomFloat(2, 0, 5),
            'management_fee' => $this->faker->randomFloat(2, 0, 2),
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'inactive']),
            'maturity_benefits' => [
                'loyalty_bonus' => $this->faker->randomFloat(2, 0, 2),
                'reinvestment_discount' => $this->faker->randomFloat(2, 0, 1),
                'additional_benefits' => $this->faker->sentence(5)
            ],
            'terms_conditions' => [
                'minimum_age' => 18,
                'documentation_required' => ['id_copy', 'proof_of_income', 'address_verification'],
                'withdrawal_notice_period' => $this->faker->numberBetween(0, 30),
                'additional_terms' => $this->faker->paragraph()
            ]
        ];
    }
    
    /**
     * Create a low-risk product
     */
    public function lowRisk(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'risk_level' => 'low',
                'product_type' => 'fixed_deposit',
                'interest_rate' => $this->faker->randomFloat(2, 5, 8),
                'liquidity_type' => 'medium',
            ];
        });
    }
    
    /**
     * Create a high-risk product
     */
    public function highRisk(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'risk_level' => 'high',
                'product_type' => 'equity_fund',
                'interest_rate' => $this->faker->randomFloat(2, 15, 25),
                'dividend_rate' => $this->faker->randomFloat(2, 5, 8),
                'liquidity_type' => 'low',
            ];
        });
    }
    
    /**
     * Create an active product
     */
    public function active(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }
}
