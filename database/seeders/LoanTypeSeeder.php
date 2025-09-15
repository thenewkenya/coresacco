<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LoanType;

class LoanTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $loanTypes = [
            [
                'name' => 'Personal Loan',
                'interest_rate' => 12.00,
                'minimum_amount' => 5000,
                'maximum_amount' => 100000,
                'term_options' => [6,12,18,24,36],
                'requirements' => ['savings_balance', 'membership_duration'],
                'description' => 'Flexible personal loan for various needs',
                'processing_fee' => 100.00,
                'status' => 'active'
            ],
            [
                'name' => 'Business Loan',
                'interest_rate' => 15.00,
                'minimum_amount' => 10000,
                'maximum_amount' => 500000,
                'term_options' => [12,18,24,36,48],
                'requirements' => ['business_registration', 'financial_statements'],
                'description' => 'Business expansion and working capital loan',
                'processing_fee' => 200.00,
                'status' => 'active'
            ],
            [
                'name' => 'Emergency Loan',
                'interest_rate' => 18.00,
                'minimum_amount' => 2000,
                'maximum_amount' => 50000,
                'term_options' => [3,6,9,12],
                'requirements' => ['emergency_documentation'],
                'description' => 'Quick emergency loan for urgent needs',
                'processing_fee' => 50.00,
                'status' => 'active'
            ],
            [
                'name' => 'Education Loan',
                'interest_rate' => 10.00,
                'minimum_amount' => 10000,
                'maximum_amount' => 200000,
                'term_options' => [12,18,24,36,48,60],
                'requirements' => ['school_admission_letter', 'fee_structure'],
                'description' => 'Education financing for school fees and related expenses',
                'processing_fee' => 150.00,
                'status' => 'active'
            ],
            [
                'name' => 'Asset Financing',
                'interest_rate' => 14.00,
                'minimum_amount' => 20000,
                'maximum_amount' => 1000000,
                'term_options' => [24,36,48,60],
                'requirements' => ['asset_quotation', 'insurance'],
                'description' => 'Asset purchase financing for vehicles, equipment, etc.',
                'processing_fee' => 300.00,
                'status' => 'active'
            ]
        ];

        foreach ($loanTypes as $loanType) {
            LoanType::create($loanType);
        }
    }
}