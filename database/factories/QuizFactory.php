<?php

namespace Database\Factories;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Quiz>
 */
class QuizFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'title' => 'اختبار: ' . fake()->randomElement(['الدرس', 'مفاهيم الوحدة']),
            'description' => fake()->sentence(),
            'passing_score' => 60,
            'time_limit_minutes' => fake()->numberBetween(5, 30),
            'max_attempts' => 3,
            'is_published' => true,
        ];
    }
}
