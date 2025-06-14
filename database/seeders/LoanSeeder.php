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
                $term = $termOptions[array_rand($termOptions)];
                
                // Determine loan status
                $statusWeights = [
                    Loan::STATUS_ACTIVE => 50,
                    Loan::STATUS_COMPLETED => 25,
                    Loan::STATUS_PENDING => 15,
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
                $collateralRequired = $requirements['collateral_required'] ?? false;

                Loan::create([
                    'member_id' => $user->id,
                    'loan_type_id' => $loanType->id,
                    'amount' => $amount,
                    'interest_rate' => $loanType->interest_rate,
                    'term_period' => $term,
                    'status' => $status,
                    'disbursement_date' => $disbursementDate,
                    'due_date' => $dueDate,
                    'collateral_details' => $collateralRequired ? [
                        'type' => 'Property Title',
                        'value' => $amount * 1.5,
                        'description' => 'Land title deed as collateral'
                    ] : null,
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
}
