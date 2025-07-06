<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalIncome = $this->faker->randomFloat(2, 30000, 150000);
        $currentYear = now()->year;
        
        return [
            'user_id' => User::factory(),
            'month' => $this->faker->numberBetween(1, 12),
            'year' => $this->faker->numberBetween($currentYear - 1, $currentYear + 1),
            'total_income' => $totalIncome,
            'total_expenses' => $totalIncome * $this->faker->randomFloat(2, 0.6, 0.9),
            'savings_target' => $totalIncome * $this->faker->randomFloat(2, 0.1, 0.3),
            'notes' => $this->faker->optional()->sentence(),
            'status' => 'active',
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
    
    public function forCurrentMonth(): static
    {
        return $this->state(fn (array $attributes) => [
            'month' => now()->month,
            'year' => now()->year,
        ]);
    }
} 