<?php

namespace Database\Seeders;

use App\Models\InvestmentProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvestmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific investment products
        $products = [
            [
                'name' => 'Fixed Deposit Premium',
                'description' => 'Secure fixed deposit with guaranteed returns. Perfect for conservative investors seeking stable income.',
                'product_type' => 'fixed_deposit',
                'minimum_investment' => 5000,
                'maximum_investment' => 5000000,
                'interest_rate' => 8.5,
                'dividend_rate' => 0,
                'risk_level' => 'low',
                'term_months' => 12,
                'compounding_frequency' => 'monthly',
                'liquidity_type' => 'medium',
                'early_withdrawal_penalty' => 2.5,
                'management_fee' => 0.5,
                'status' => 'active',
                'maturity_benefits' => [
                    'loyalty_bonus' => 0.5,
                    'reinvestment_discount' => 0.25,
                    'additional_benefits' => 'Free account maintenance for 6 months'
                ],
                'terms_conditions' => [
                    'minimum_age' => 18,
                    'documentation_required' => ['id_copy', 'proof_of_income'],
                    'withdrawal_notice_period' => 30,
                    'additional_terms' => 'Minimum investment period of 6 months required'
                ]
            ],
            [
                'name' => 'Money Market Fund',
                'description' => 'High liquidity money market fund with competitive returns and easy access to funds.',
                'product_type' => 'money_market',
                'minimum_investment' => 1000,
                'maximum_investment' => 10000000,
                'interest_rate' => 6.2,
                'dividend_rate' => 0,
                'risk_level' => 'low',
                'term_months' => null,
                'compounding_frequency' => 'daily',
                'liquidity_type' => 'high',
                'early_withdrawal_penalty' => 0,
                'management_fee' => 0.75,
                'status' => 'active',
                'maturity_benefits' => [
                    'loyalty_bonus' => 0,
                    'reinvestment_discount' => 0,
                    'additional_benefits' => 'No penalties for withdrawals'
                ],
                'terms_conditions' => [
                    'minimum_age' => 18,
                    'documentation_required' => ['id_copy'],
                    'withdrawal_notice_period' => 0,
                    'additional_terms' => 'Instant withdrawal available'
                ]
            ],
            [
                'name' => 'Government Bond Fund',
                'description' => 'Government-backed bond fund offering stable returns with minimal risk.',
                'product_type' => 'government_bond',
                'minimum_investment' => 10000,
                'maximum_investment' => 50000000,
                'interest_rate' => 12.5,
                'dividend_rate' => 0,
                'risk_level' => 'low',
                'term_months' => 24,
                'compounding_frequency' => 'semi_annually',
                'liquidity_type' => 'medium',
                'early_withdrawal_penalty' => 1.0,
                'management_fee' => 0.25,
                'status' => 'active',
                'maturity_benefits' => [
                    'loyalty_bonus' => 1.0,
                    'reinvestment_discount' => 0.5,
                    'additional_benefits' => 'Tax-free interest income'
                ],
                'terms_conditions' => [
                    'minimum_age' => 18,
                    'documentation_required' => ['id_copy', 'proof_of_income', 'tax_clearance'],
                    'withdrawal_notice_period' => 14,
                    'additional_terms' => 'Government guarantee on principal and interest'
                ]
            ],
            [
                'name' => 'Equity Growth Fund',
                'description' => 'Equity-focused fund targeting capital appreciation through diversified stock investments.',
                'product_type' => 'equity_fund',
                'minimum_investment' => 25000,
                'maximum_investment' => 100000000,
                'interest_rate' => 18.0,
                'dividend_rate' => 5.5,
                'risk_level' => 'high',
                'term_months' => null,
                'compounding_frequency' => 'quarterly',
                'liquidity_type' => 'low',
                'early_withdrawal_penalty' => 5.0,
                'management_fee' => 1.5,
                'status' => 'active',
                'maturity_benefits' => [
                    'loyalty_bonus' => 2.0,
                    'reinvestment_discount' => 1.0,
                    'additional_benefits' => 'Professional portfolio management'
                ],
                'terms_conditions' => [
                    'minimum_age' => 21,
                    'documentation_required' => ['id_copy', 'proof_of_income', 'investment_experience'],
                    'withdrawal_notice_period' => 30,
                    'additional_terms' => 'High risk investment with potential for significant gains and losses'
                ]
            ],
            [
                'name' => 'Balanced Growth Fund',
                'description' => 'Balanced portfolio of stocks and bonds designed for moderate risk tolerance.',
                'product_type' => 'balanced_fund',
                'minimum_investment' => 15000,
                'maximum_investment' => 75000000,
                'interest_rate' => 14.5,
                'dividend_rate' => 3.8,
                'risk_level' => 'medium',
                'term_months' => null,
                'compounding_frequency' => 'quarterly',
                'liquidity_type' => 'medium',
                'early_withdrawal_penalty' => 3.0,
                'management_fee' => 1.25,
                'status' => 'active',
                'maturity_benefits' => [
                    'loyalty_bonus' => 1.5,
                    'reinvestment_discount' => 0.75,
                    'additional_benefits' => 'Diversified investment strategy'
                ],
                'terms_conditions' => [
                    'minimum_age' => 18,
                    'documentation_required' => ['id_copy', 'proof_of_income'],
                    'withdrawal_notice_period' => 21,
                    'additional_terms' => 'Moderate risk with balanced growth potential'
                ]
            ],
            [
                'name' => 'Retirement Savings Plan',
                'description' => 'Long-term retirement savings with tax advantages and compound growth.',
                'product_type' => 'retirement_fund',
                'minimum_investment' => 5000,
                'maximum_investment' => 200000000,
                'interest_rate' => 16.0,
                'dividend_rate' => 0,
                'risk_level' => 'medium',
                'term_months' => 60,
                'compounding_frequency' => 'annually',
                'liquidity_type' => 'low',
                'early_withdrawal_penalty' => 10.0,
                'management_fee' => 0.8,
                'status' => 'active',
                'maturity_benefits' => [
                    'loyalty_bonus' => 3.0,
                    'reinvestment_discount' => 1.5,
                    'additional_benefits' => 'Tax deductions on contributions'
                ],
                'terms_conditions' => [
                    'minimum_age' => 18,
                    'documentation_required' => ['id_copy', 'proof_of_income', 'employment_letter'],
                    'withdrawal_notice_period' => 60,
                    'additional_terms' => 'Early withdrawal penalties apply before retirement age'
                ]
            ]
        ];

        foreach ($products as $product) {
            InvestmentProduct::create($product);
        }

        // Create additional random products using factory
        InvestmentProduct::factory()->count(10)->active()->create();
    }
}
