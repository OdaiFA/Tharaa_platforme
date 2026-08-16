<?php

namespace Database\Factories;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\QuizQuestion>
 */
class QuizQuestionFactory extends Factory
{
    public function definition(): array
    {
        $options = collect(fake()->unique()->words(3))->map(fn ($w) => ucfirst($w))->values()->all();

        return [
            'quiz_id' => Quiz::factory(),
            'question' => fake()->sentence() . '؟',
            'type' => 'multiple_choice',
            'options' => $options,
            'correct_answer' => [array_rand($options)],
            'points' => 1,
            'order_index' => 0,
        ];
    }
}
