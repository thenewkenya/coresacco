<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\User;
use App\Models\LoanType;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('membership_status', 'active')->whereNotNull('member_number')->get();
        $loanTypes = LoanType::where('status', 'active')->get();

        // 60% of active members should have at least one loan
        $usersWithLoans = $users->random(intval($users->count() * 0.6));

        foreach ($usersWithLoans as $user) {
            $numLoans = rand(1, 2); // Each member can have 1-2 loans

            for ($i = 0; $i < $numLoans; $i++) {
                $loanType = $loanTypes->random();
                
                // Calculate loan amount within limits
                $minAmount = $loanType->minimum_amount;
                $maxAmount = min($loanType->maximum_amount, 500000); // Cap at 500K for seeding
                $amount = rand($minAmount, $maxAmount);
                
                // Calculate term from available options
                $termOptions = $loanType->term_options;
                
                // Ensure term_options is an array
                if (is_string($termOptions)) {
                    $termOptions = json_decode($termOptions, true);
                }
                
                // Fallback if term_options is still not an array
                if (!is_array($termOptions) || empty($termOptions)) {
                    $termOptions = [12, 24, 36]; // Default terms
                }
                
                $term = $termOptions[array_rand($termOptions)];
                
                // Determine loan status with more realistic distribution
                $statusWeights = [
                    Loan::STATUS_ACTIVE => 40,
                    Loan::STATUS_COMPLETED => 30,
                    Loan::STATUS_PENDING => 20,
                    Loan::STATUS_DISBURSED => 8,
                    Loan::STATUS_DEFAULTED => 2,
                ];
                
                $status = $this->getWeightedRandom($statusWeights);
                
                // Set dates based on status
                $disbursementDate = null;
                $dueDate = null;
                
                if (in_array($status, [Loan::STATUS_ACTIVE, Loan::STATUS_COMPLETED, Loan::STATUS_DEFAULTED])) {
                    $disbursementDate = now()->subMonths(rand(1, 24));
                    $dueDate = $disbursementDate->copy()->addMonths($term);
                } elseif ($status === Loan::STATUS_DISBURSED) {
                    $disbursementDate = now()->subDays(rand(1, 7));
                    $dueDate = $disbursementDate->copy()->addMonths($term);
                }

                $requirements = $loanType->requirements;
                
                // Ensure requirements is an array
                if (is_string($requirements)) {
                    $requirements = json_decode($requirements, true);
                }
                
                // Fallback if requirements is still not an array
                if (!is_array($requirements)) {
                    $requirements = [];
                }
                
                $collateralRequired = $requirements['collateral_required'] ?? false;

                // Generate realistic approval dates for approved loans
                $approvedAt = null;
                $approvedBy = null;
                
                if (in_array($status, [Loan::STATUS_ACTIVE, Loan::STATUS_COMPLETED, Loan::STATUS_DEFAULTED, Loan::STATUS_DISBURSED])) {
                    $approvedAt = now()->subMonths(rand(1, 36))->addDays(rand(1, 14));
                    $approvedBy = 1; // Admin user ID
                }

                Loan::create([
                    'member_id' => $user->id,
                    'loan_type_id' => $loanType->id,
                    'amount' => $amount,
                    'interest_rate' => $loanType->interest_rate,
                    'term_period' => $term,
                    'status' => $status,
                    'approved_at' => $approvedAt,
                    'approved_by' => $approvedBy,
                    'approval_notes' => $this->getRandomLoanNotes($status),
                    'disbursement_date' => $disbursementDate,
                    'due_date' => $dueDate,
                    'collateral_details' => $collateralRequired ? [
                        'type' => 'Property Title',
                        'value' => $amount * 1.5,
                        'description' => 'Land title deed as collateral'
                    ] : null,
                    'metadata' => [
                        'purpose' => $this->getRandomLoanPurpose($loanType->name),
                        'application_notes' => $this->getRandomLoanNotes($status),
                    ],
                ]);
            }
        }
    }

    /**
     * Get a weighted random value from an array
     */
    private function getWeightedRandom(array $weights): string
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($weights as $value => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $value;
            }
        }
        
        return array_key_first($weights);
    }

    /**
     * Get a random loan purpose based on loan type
     */
    private function getRandomLoanPurpose(string $loanType): string
    {
        $purposes = [
            'Personal Loan' => [
                'Medical expenses',
                'Home improvement',
                'Wedding expenses',
                'Debt consolidation',
                'Family emergency',
                'Vacation',
                'Education fees',
                'Vehicle purchase'
            ],
            'Business Loan' => [
                'Working capital',
                'Equipment purchase',
                'Inventory expansion',
                'Business expansion',
                'Marketing campaign',
                'Staff recruitment',
                'Technology upgrade',
                'Premises renovation'
            ],
            'Emergency Loan' => [
                'Medical emergency',
                'Family crisis',
                'Urgent home repair',
                'School fees payment',
                'Funeral expenses',
                'Legal fees',
                'Emergency travel',
                'Utility bills'
            ],
            'Education Loan' => [
                'University fees',
                'School fees',
                'Books and supplies',
                'Accommodation',
                'Transportation',
                'Exam fees',
                'Research materials',
                'Study abroad'
            ],
            'Asset Financing' => [
                'Vehicle purchase',
                'Equipment acquisition',
                'Property development',
                'Machinery purchase',
                'Technology investment',
                'Furniture and fixtures',
                'Construction materials',
                'Agricultural equipment'
            ]
        ];

        $typePurposes = $purposes[$loanType] ?? ['General purpose'];
        return $typePurposes[array_rand($typePurposes)];
    }

    /**
     * Get random loan notes based on status
     */
    private function getRandomLoanNotes(string $status): string
    {
        $notes = [
            Loan::STATUS_PENDING => [
                'Application under review',
                'Awaiting documentation',
                'Credit check in progress',
                'Under committee review',
                'Pending guarantor approval'
            ],
            Loan::STATUS_ACTIVE => [
                'Regular payments on schedule',
                'Good payment history',
                'No issues reported',
                'Member in good standing',
                'Payments up to date'
            ],
            Loan::STATUS_COMPLETED => [
                'Successfully repaid in full',
                'Loan completed on time',
                'Excellent payment record',
                'Fully settled',
                'No outstanding balance'
            ],
            Loan::STATUS_DEFAULTED => [
                'Payment default occurred',
                'Collections in progress',
                'Member unresponsive',
                'Legal action pending',
                'Outstanding arrears'
            ],
            Loan::STATUS_DISBURSED => [
                'Recently disbursed',
                'New loan account',
                'First payment pending',
                'Monitoring required',
                'Fresh disbursement'
            ]
        ];

        $statusNotes = $notes[$status] ?? ['No notes'];
        return $statusNotes[array_rand($statusNotes)];
    }
}
