<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\GoalContribution>
 */
class GoalContributionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'goal_id' => Goal::factory(),
            'user_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 50, 5000),
            'account_id' => null,
            'note' => fake()->optional(0.5)->sentence(3),
            'contribution_date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
        ];
    }
}
