<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ArticleCategory>
 */
class ArticleCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['الادخار', 'الاستثمار', 'الميزانية', 'الثقافة المالية', 'ريادة الأعمال']),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
        ];
    }
}
