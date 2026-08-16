<?php

namespace Database\Factories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'module_id' => Module::factory(),
            'title' => fake()->randomElement([
                'ما هي الثقافة المالية؟',
                'كيف تنشئ ميزانيتك الأولى',
                'التوفير في الممارسة اليومية',
                'قواعد الاستثمار الآمن',
                'تجنب الديون المتراكمة',
            ]),
            'content' => fake()->paragraphs(3, true),
            'video_url' => fake()->optional(0.5)->url(),
            'duration_minutes' => fake()->numberBetween(5, 45),
            'order_index' => fake()->numberBetween(0, 20),
            'is_published' => true,
            'resources' => null,
        ];
    }
}
