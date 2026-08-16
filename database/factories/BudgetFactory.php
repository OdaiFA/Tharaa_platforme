<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Budget>
 */
class BudgetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => 'ميزانية ' . fake()->monthName(),
            'total_amount' => fake()->randomFloat(2, 1000, 30000),
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
            'currency' => 'SAR',
            'alert_percentage' => 80,
            'is_active' => true,
        ];
    }
}
