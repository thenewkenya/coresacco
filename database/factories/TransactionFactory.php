<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\User;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 10, 5000);
        $balanceBefore = $this->faker->randomFloat(2, 0, 10000);
        
        return [
            'account_id' => Account::factory(),
            'member_id' => User::factory(),
            'loan_id' => null,
            'type' => $this->faker->randomElement([
                Transaction::TYPE_DEPOSIT,
                Transaction::TYPE_WITHDRAWAL,
                Transaction::TYPE_TRANSFER,
            ]),
            'amount' => $amount,
            'description' => $this->faker->sentence(4),
            'reference_number' => 'TXN' . date('Ymd') . str_pad($this->faker->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'status' => Transaction::STATUS_PENDING,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceBefore + $amount, // Default for deposit
            'metadata' => [],
        ];
    }

    public function deposit(): static
    {
        return $this->state(function (array $attributes) {
            $amount = $attributes['amount'] ?? $this->faker->randomFloat(2, 10, 5000);
            $balanceBefore = $attributes['balance_before'] ?? $this->faker->randomFloat(2, 0, 10000);
            
            return [
                'type' => Transaction::TYPE_DEPOSIT,
                'balance_after' => $balanceBefore + $amount,
            ];
        });
    }

    public function withdrawal(): static
    {
        return $this->state(function (array $attributes) {
            $amount = $attributes['amount'] ?? $this->faker->randomFloat(2, 10, 1000);
            $balanceBefore = $attributes['balance_before'] ?? $this->faker->randomFloat(2, $amount, 10000);
            
            return [
                'type' => Transaction::TYPE_WITHDRAWAL,
                'balance_after' => $balanceBefore - $amount,
            ];
        });
    }

    public function loanDisbursement(): static
    {
        return $this->state(function (array $attributes) {
            $amount = $attributes['amount'] ?? $this->faker->randomFloat(2, 1000, 50000);
            $balanceBefore = $attributes['balance_before'] ?? $this->faker->randomFloat(2, 0, 10000);
            
            return [
                'type' => Transaction::TYPE_LOAN_DISBURSEMENT,
                'loan_id' => Loan::factory(),
                'balance_after' => $balanceBefore + $amount,
            ];
        });
    }

    public function loanRepayment(): static
    {
        return $this->state(function (array $attributes) {
            $amount = $attributes['amount'] ?? $this->faker->randomFloat(2, 100, 5000);
            $balanceBefore = $attributes['balance_before'] ?? $this->faker->randomFloat(2, $amount, 10000);
            
            return [
                'type' => Transaction::TYPE_LOAN_REPAYMENT,
                'loan_id' => Loan::factory(),
                'balance_after' => $balanceBefore - $amount,
            ];
        });
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Transaction::STATUS_PENDING,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Transaction::STATUS_FAILED,
        ]);
    }
} 