<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereNotNull('member_number')->get();

        foreach ($users as $user) {
            // Create savings account for each member (100% have savings)
            Account::updateOrCreate(
                [
                    'member_id' => $user->id,
                    'account_type' => Account::TYPE_SAVINGS
                ],
                [
                    'account_number' => $this->generateAccountNumber('SAV'),
                    'balance' => $this->getRealisticSavingsBalance($user),
                    'status' => Account::STATUS_ACTIVE,
                    'currency' => 'KES',
                ]
            );

            // 80% chance of having a shares account
            if (rand(1, 10) <= 8) {
                Account::updateOrCreate(
                    [
                        'member_id' => $user->id,
                        'account_type' => Account::TYPE_SHARES
                    ],
                    [
                        'account_number' => $this->generateAccountNumber('SHR'),
                        'balance' => $this->getRealisticSharesBalance($user),
                        'status' => Account::STATUS_ACTIVE,
                        'currency' => 'KES',
                    ]
                );
            }

            // 40% chance of having a deposits account
            if (rand(1, 10) <= 4) {
                Account::updateOrCreate(
                    [
                        'member_id' => $user->id,
                        'account_type' => Account::TYPE_DEPOSITS
                    ],
                    [
                        'account_number' => $this->generateAccountNumber('DEP'),
                        'balance' => $this->getRealisticDepositsBalance($user),
                        'status' => Account::STATUS_ACTIVE,
                        'currency' => 'KES',
                    ]
                );
            }

            // 20% chance of having an emergency fund
            if (rand(1, 10) <= 2) {
                Account::updateOrCreate(
                    [
                        'member_id' => $user->id,
                        'account_type' => Account::TYPE_EMERGENCY_FUND
                    ],
                    [
                        'account_number' => $this->generateAccountNumber('EMG'),
                        'balance' => $this->getRealisticEmergencyFundBalance($user),
                        'status' => Account::STATUS_ACTIVE,
                        'currency' => 'KES',
                    ]
                );
            }

            // 15% chance of having a retirement account
            if (rand(1, 10) <= 1.5) {
                Account::updateOrCreate(
                    [
                        'member_id' => $user->id,
                        'account_type' => Account::TYPE_RETIREMENT
                    ],
                    [
                        'account_number' => $this->generateAccountNumber('RET'),
                        'balance' => $this->getRealisticRetirementBalance($user),
                        'status' => Account::STATUS_ACTIVE,
                        'currency' => 'KES',
                    ]
                );
            }
        }
    }

    /**
     * Generate a unique account number
     */
    private function generateAccountNumber(string $prefix): string
    {
        do {
            $number = $prefix . date('Y') . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (Account::where('account_number', $number)->exists());

        return $number;
    }

    /**
     * Get realistic savings balance based on user profile
     */
    private function getRealisticSavingsBalance(User $user): int
    {
        $monthsSinceJoining = $user->joining_date ? now()->diffInMonths($user->joining_date) : 12;
        
        // Base savings based on membership duration
        $baseAmount = min($monthsSinceJoining * 2000, 100000); // Max 100K
        
        // Add random variation
        $variation = rand(-20000, 50000);
        
        return max(1000, $baseAmount + $variation); // Minimum 1K
    }

    /**
     * Get realistic shares balance
     */
    private function getRealisticSharesBalance(User $user): int
    {
        $monthsSinceJoining = $user->joining_date ? now()->diffInMonths($user->joining_date) : 12;
        
        // Shares typically grow over time
        $baseAmount = min($monthsSinceJoining * 1000, 50000); // Max 50K
        
        // Add random variation
        $variation = rand(-10000, 20000);
        
        return max(2000, $baseAmount + $variation); // Minimum 2K
    }

    /**
     * Get realistic deposits balance
     */
    private function getRealisticDepositsBalance(User $user): int
    {
        // Deposits are typically larger amounts
        $baseAmount = rand(20000, 300000);
        
        // Higher chance of larger amounts for longer members
        $monthsSinceJoining = $user->joining_date ? now()->diffInMonths($user->joining_date) : 12;
        if ($monthsSinceJoining > 24) {
            $baseAmount = rand(50000, 500000);
        }
        
        return $baseAmount;
    }

    /**
     * Get realistic emergency fund balance
     */
    private function getRealisticEmergencyFundBalance(User $user): int
    {
        // Emergency funds are typically 3-6 months of expenses
        $baseAmount = rand(30000, 150000);
        
        return $baseAmount;
    }

    /**
     * Get realistic retirement balance
     */
    private function getRealisticRetirementBalance(User $user): int
    {
        $monthsSinceJoining = $user->joining_date ? now()->diffInMonths($user->joining_date) : 12;
        
        // Retirement grows significantly over time
        $baseAmount = min($monthsSinceJoining * 3000, 200000); // Max 200K
        
        // Add random variation
        $variation = rand(-20000, 50000);
        
        return max(10000, $baseAmount + $variation); // Minimum 10K
    }
}
