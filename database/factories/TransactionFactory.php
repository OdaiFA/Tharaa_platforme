<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['income', 'expense']);

        return [
            'user_id' => User::factory(),
            'account_id' => Account::factory(),
            'category_id' => Category::query()->where('type', $type)->inRandomOrder()->first()?->id,
            'type' => $type,
            'amount' => fake()->randomFloat(2, 5, 5000),
            'description' => fake()->optional(0.7)->sentence(3),
            'transaction_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'is_recurring' => false,
        ];
    }

    public function income(): static
    {
        return $this->state(fn () => [
            'type' => 'income',
            'category_id' => Category::query()->income()->inRandomOrder()->first()?->id,
        ]);
    }

    public function expense(): static
    {
        return $this->state(fn () => [
            'type' => 'expense',
            'category_id' => Category::query()->expense()->inRandomOrder()->first()?->id,
        ]);
    }
}
