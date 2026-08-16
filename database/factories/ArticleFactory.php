<?php

namespace Database\Factories;

use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                '10 عادات مالية ذكية تبدأ بها عامك',
                'كيف تبني صندوق طوارئ خلال 6 أشهر',
                'دليل المبتدئين للاستثمار في الأسواق',
                'أخطاء شائعة في إدارة الميزانية الشخصية',
                'الفرق بين الأصل والالتزام ببساطة',
            ]),
            'content' => fake()->paragraphs(6, true),
            'excerpt' => fake()->sentence(10),
            'category_id' => ArticleCategory::factory(),
            'author_id' => User::factory(),
            'is_published' => true,
            'published_at' => now(),
            'views_count' => fake()->numberBetween(0, 1000),
        ];
    }
}
