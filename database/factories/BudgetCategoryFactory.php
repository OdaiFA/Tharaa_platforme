<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\BudgetCategory>
 */
class BudgetCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'budget_id' => Budget::factory(),
            'category_id' => Category::query()->expense()->inRandomOrder()->first()?->id ?? Category::factory(),
            'limit_amount' => fake()->randomFloat(2, 100, 10000),
            'alert_percentage' => 80,
        ];
    }
}
