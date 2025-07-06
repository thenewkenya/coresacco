<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\User;
use App\Models\LoanType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 5000, 100000);
        
        return [
            'member_id' => User::factory(),
            'loan_type_id' => LoanType::factory(),
            'amount' => $amount,
            'interest_rate' => $this->faker->randomFloat(2, 5, 25),
            'term_period' => $this->faker->randomElement([6, 12, 18, 24, 36]),
            'status' => 'pending',
            'processing_fee' => $amount * 0.02, // 2% processing fee
            'total_payable' => $amount * (1 + ($this->faker->randomFloat(2, 5, 25) / 100)),
            'amount_paid' => 0,
            'collateral_details' => json_encode([
                'type' => $this->faker->randomElement(['property', 'vehicle', 'savings', 'guarantor']),
                'description' => $this->faker->sentence(),
                'estimated_value' => $amount * 1.2,
            ]),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }

    public function disbursed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'disbursed',
            'disbursement_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'due_date' => $this->faker->dateTimeBetween('now', '+2 years'),
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'disbursement_date' => $this->faker->dateTimeBetween('-6 months', '-1 month'),
            'due_date' => $this->faker->dateTimeBetween('now', '+2 years'),
            'amount_paid' => $this->faker->randomFloat(2, 0, $attributes['amount'] * 0.5),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'disbursement_date' => $this->faker->dateTimeBetween('-2 years', '-1 year'),
            'due_date' => $this->faker->dateTimeBetween('-1 year', '-6 months'),
            'amount_paid' => $attributes['total_payable'],
        ]);
    }

    public function defaulted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'defaulted',
            'disbursement_date' => $this->faker->dateTimeBetween('-2 years', '-1 year'),
            'due_date' => $this->faker->dateTimeBetween('-1 year', '-3 months'),
            'amount_paid' => $this->faker->randomFloat(2, 0, $attributes['amount'] * 0.3),
        ]);
    }
} 