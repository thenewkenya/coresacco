<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' Branch',
            'code' => strtoupper($this->faker->lexify('BR???')),
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'manager_id' => null, // Can be set separately
            'status' => 'active',
            'opening_date' => $this->faker->dateTimeBetween('-5 years', '-1 year'),
            'working_hours' => [
                'monday' => ['open' => '08:00', 'close' => '17:00'],
                'tuesday' => ['open' => '08:00', 'close' => '17:00'],
                'wednesday' => ['open' => '08:00', 'close' => '17:00'],
                'thursday' => ['open' => '08:00', 'close' => '17:00'],
                'friday' => ['open' => '08:00', 'close' => '17:00'],
                'saturday' => ['open' => '08:00', 'close' => '13:00'],
                'sunday' => ['open' => '09:00', 'close' => '12:00'],
            ],
            'coordinates' => [
                'latitude' => $this->faker->latitude(-5, 5), // Kenya region
                'longitude' => $this->faker->longitude(32, 42), // Kenya region
            ],
        ];
    }

    public function withManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'manager_id' => User::factory(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
} 