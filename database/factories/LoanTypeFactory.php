<?php

namespace Database\Factories;

use App\Models\LoanType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoanType>
 */
class LoanTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Personal Loan',
            'Business Loan',
            'Emergency Loan',
            'Development Loan',
            'Education Loan',
            'Agriculture Loan'
        ]);
        
        $termOptions = [12, 24, 36, 48, 60];
        
        return [
            'name' => $name,
            'interest_rate' => $this->faker->randomFloat(2, 5, 25),
            'minimum_amount' => $this->faker->randomFloat(2, 1000, 5000),
            'maximum_amount' => $this->faker->randomFloat(2, 50000, 500000),
            'term_options' => json_encode($termOptions),
            'requirements' => json_encode([
                'minimum_age' => 18,
                'employment_status' => 'employed',
                'minimum_income' => 20000,
                'collateral_required' => false
            ]),
            'description' => $this->faker->sentence(),
            'processing_fee' => $this->faker->randomFloat(2, 0.5, 5),
            'status' => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
} 