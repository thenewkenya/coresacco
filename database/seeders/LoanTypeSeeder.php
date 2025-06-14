<?php

namespace Database\Seeders;

use App\Models\LoanType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
                'description' => 'General purpose personal loan for members',
                'interest_rate' => 12.50,
                'maximum_amount' => 500000,
                'minimum_amount' => 5000,
                'term_options' => [6, 12, 18, 24, 36], // Available terms in months
                'processing_fee' => 2.00, // percentage
                'requirements' => [
                    'guarantors' => 2,
                    'collateral_required' => false,
                    'minimum_membership_months' => 6,
                    'documents' => ['National ID', 'Payslip', 'Bank Statement']
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Emergency Loan',
                'description' => 'Quick emergency loan for urgent needs',
                'interest_rate' => 15.00,
                'maximum_amount' => 100000,
                'minimum_amount' => 1000,
                'term_options' => [3, 6, 9, 12], // Available terms in months
                'processing_fee' => 1.50,
                'requirements' => [
                    'guarantors' => 0,
                    'collateral_required' => false,
                    'minimum_membership_months' => 3,
                    'documents' => ['National ID']
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Development Loan',
                'description' => 'Long-term loan for development projects',
                'interest_rate' => 10.00,
                'maximum_amount' => 2000000,
                'minimum_amount' => 50000,
                'term_options' => [12, 24, 36, 48, 60], // Available terms in months
                'processing_fee' => 3.00,
                'requirements' => [
                    'guarantors' => 3,
                    'collateral_required' => true,
                    'minimum_membership_months' => 12,
                    'documents' => ['National ID', 'Payslip', 'Bank Statement', 'Collateral Documents', 'Project Proposal']
                ],
                'status' => 'active',
            ],
            [
                'name' => 'School Fees Loan',
                'description' => 'Educational loan for school fees payment',
                'interest_rate' => 8.00,
                'maximum_amount' => 300000,
                'minimum_amount' => 10000,
                'term_options' => [6, 12, 18, 24], // Available terms in months
                'processing_fee' => 1.00,
                'requirements' => [
                    'guarantors' => 1,
                    'collateral_required' => false,
                    'minimum_membership_months' => 6,
                    'documents' => ['National ID', 'School Fee Structure', 'Admission Letter']
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Asset Financing',
                'description' => 'Loan for purchasing assets like vehicles, equipment',
                'interest_rate' => 13.00,
                'maximum_amount' => 1500000,
                'minimum_amount' => 100000,
                'term_options' => [12, 24, 36, 48], // Available terms in months
                'processing_fee' => 2.50,
                'requirements' => [
                    'guarantors' => 2,
                    'collateral_required' => true,
                    'minimum_membership_months' => 12,
                    'documents' => ['National ID', 'Payslip', 'Bank Statement', 'Asset Proforma Invoice']
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Salary Advance',
                'description' => 'Short-term advance against salary',
                'interest_rate' => 5.00,
                'maximum_amount' => 50000,
                'minimum_amount' => 5000,
                'term_options' => [1, 2, 3, 6], // Available terms in months
                'processing_fee' => 0.50,
                'requirements' => [
                    'guarantors' => 0,
                    'collateral_required' => false,
                    'minimum_membership_months' => 3,
                    'documents' => ['National ID', 'Latest Payslip']
                ],
                'status' => 'active',
            ]
        ];

        foreach ($loanTypes as $loanType) {
            LoanType::updateOrCreate(
                ['name' => $loanType['name']],
                $loanType
            );
        }
    }
}
