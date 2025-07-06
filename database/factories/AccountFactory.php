<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_number' => Account::generateAccountNumber(),
            'member_id' => User::factory(),
            'account_type' => $this->faker->randomElement(Account::getAccountTypes()),
            'balance' => $this->faker->randomFloat(2, 0, 50000),
            'status' => Account::STATUS_ACTIVE,
            'currency' => 'KES',
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Account::STATUS_ACTIVE,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
            'status_reason' => 'Account under investigation',
        ]);
    }

    public function savings(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_type' => Account::TYPE_SAVINGS,
        ]);
    }

    public function shares(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_type' => Account::TYPE_SHARES,
        ]);
    }

    public function withBalance(float $balance): static
    {
        return $this->state(fn (array $attributes) => [
            'balance' => $balance,
        ]);
    }
} 