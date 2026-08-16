<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Module>
 */
class ModuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->randomElement(['مقدمة', 'المفاهيم الأساسية', 'التطبيق العملي', 'المراجعة النهائية']),
            'description' => fake()->sentence(),
            'order_index' => fake()->numberBetween(0, 10),
            'is_published' => true,
        ];
    }
}
