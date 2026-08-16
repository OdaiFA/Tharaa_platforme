<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'أساسيات الميزانية الشخصية',
                'الادخار الذكي للمستقبل',
                'مقدمة في الاستثمار',
                'التعامل الآمن مع المدفوعات الإلكترونية',
                'ريادة الأعمال للشباب',
            ]),
            'description' => fake()->paragraph(2),
            'level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'duration_hours' => fake()->numberBetween(1, 12),
            'is_published' => true,
            'certificate_enabled' => true,
            'passing_score' => 60,
            'created_by' => User::factory(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }
}
