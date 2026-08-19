<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'type' => 'expense',
            'icon' => null,
            'color' => null,
            'is_system' => false,
        ];
    }

    public function income(): static
    {
        return $this->state(fn () => ['type' => 'income']);
    }

    public function system(): static
    {
        return $this->state(fn () => ['is_system' => true]);
    }
}
