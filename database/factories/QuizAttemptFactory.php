<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\QuizAttempt>
 */
class QuizAttemptFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'quiz_id' => Quiz::factory(),
            'score' => fake()->numberBetween(0, 100),
            'total_points' => 4,
            'earned_points' => fake()->numberBetween(0, 4),
            'is_passed' => fake()->boolean(60),
            'attempt_number' => 1,
            'started_at' => now()->subMinutes(5),
            'completed_at' => now(),
        ];
    }
}
