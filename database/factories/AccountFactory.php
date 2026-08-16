<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['الحساب الجاري', 'محفظة نقدية', 'محفظة إلكترونية', 'حساب التوفير']),
            'type' => fake()->randomElement(['bank', 'cash', 'electronic']),
            'balance' => fake()->randomFloat(2, 0, 50000),
            'currency' => 'SAR',
            'is_active' => true,
            'description' => null,
        ];
    }
}
