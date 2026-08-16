<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Goal>
 */
class GoalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['شراء سيارة', 'رحلة العائلة', 'صندوق الطوارئ', 'جهاز جديد', 'دراسة إضافية']),
            'target_amount' => fake()->randomFloat(2, 1000, 100000),
            'current_amount' => fake()->randomFloat(2, 0, 5000),
            'deadline' => fake()->optional()->dateTimeBetween('+1 month', '+3 years')?->format('Y-m-d'),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'status' => 'active',
            'icon' => fake()->randomElement(['🎯', '🚗', '✈️', '💼', '🏠']),
        ];
    }
}
