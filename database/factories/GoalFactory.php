<?php

namespace Database\Factories;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Goal>
 */
class GoalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $targetAmount = $this->faker->randomFloat(2, 5000, 100000);
        $savedAmount = $this->faker->randomFloat(2, 0, $targetAmount * 0.8);
        
        return [
            'member_id' => User::factory(),
            'title' => $this->faker->randomElement([
                'New Car Fund',
                'House Down Payment',
                'Emergency Fund',
                'Vacation Savings',
                'Education Fund',
                'Business Capital'
            ]),
            'description' => $this->faker->sentence(),
            'target_amount' => $targetAmount,
            'current_amount' => $savedAmount,
            'target_date' => $this->faker->dateTimeBetween('+3 months', '+2 years'),
            'type' => $this->faker->randomElement([
                'emergency_fund',
                'home_purchase',
                'education',
                'retirement',
                'custom'
            ]),
            'status' => 'active',
            'auto_save_amount' => $this->faker->randomFloat(2, 100, 2000),
            'auto_save_frequency' => $this->faker->randomElement(['weekly', 'monthly']),
        ];
    }

    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $targetAmount = $attributes['target_amount'] ?? 10000;
            
            return [
                'current_amount' => $targetAmount,
                'status' => 'completed',
                'completed_at' => now(),
            ];
        });
    }

    public function paused(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paused',
        ]);
    }
} 