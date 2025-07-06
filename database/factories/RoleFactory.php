<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement(['Administrator', 'Manager', 'Staff', 'Member', 'Guest']);
        
        return [
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '_', $name)),
            'description' => $this->faker->sentence(),
            'permissions' => $this->faker->randomElements([
                'view_dashboard',
                'manage_users',
                'view_reports',
                'process_transactions',
                'approve_loans',
                'view_accounts',
                'manage_settings',
            ], rand(2, 5)),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Administrator',
            'slug' => 'admin',
            'permissions' => [
                'manage_users',
                'view_reports',
                'approve_loans',
                'process_transactions',
                'manage_settings',
                'view_accounts',
                'view_dashboard',
            ],
        ]);
    }

    public function member(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Member',
            'slug' => 'member',
            'permissions' => [
                'view_dashboard',
                'view_accounts',
                'apply_loan',
            ],
        ]);
    }

    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Staff',
            'slug' => 'staff',
            'permissions' => [
                'view_dashboard',
                'process_transactions',
                'view_accounts',
                'view_member_data',
            ],
        ]);
    }
} 