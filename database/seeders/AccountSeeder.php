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
            // Create savings account for each member
            Account::updateOrCreate(
                [
                    'member_id' => $user->id,
                    'account_type' => Account::TYPE_SAVINGS
                ],
                [
                    'account_number' => $this->generateAccountNumber('SAV'),
                    'balance' => rand(1000, 100000), // Random balance between 1K and 100K
                    'status' => Account::STATUS_ACTIVE,
                    'currency' => 'KES',
                ]
            );

            // 70% chance of having a shares account
            if (rand(1, 10) <= 7) {
                Account::updateOrCreate(
                    [
                        'member_id' => $user->id,
                        'account_type' => Account::TYPE_SHARES
                    ],
                    [
                        'account_number' => $this->generateAccountNumber('SHR'),
                        'balance' => rand(5000, 50000), // Random shares between 5K and 50K
                        'status' => Account::STATUS_ACTIVE,
                        'currency' => 'KES',
                    ]
                );
            }

            // 30% chance of having a deposits account
            if (rand(1, 10) <= 3) {
                Account::updateOrCreate(
                    [
                        'member_id' => $user->id,
                        'account_type' => Account::TYPE_DEPOSITS
                    ],
                    [
                        'account_number' => $this->generateAccountNumber('DEP'),
                        'balance' => rand(10000, 200000), // Random deposits between 10K and 200K
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
}
