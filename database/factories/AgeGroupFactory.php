<?php

namespace Database\Factories;

use App\Models\AgeGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AgeGroup>
 */
class AgeGroupFactory extends Factory
{
    protected $model = AgeGroup::class;

    public function definition(): array
    {
        $min = fake()->numberBetween(0, 50);

        return [
            'name' => fake()->words(2, true),
            'min_age' => $min,
            'max_age' => $min + fake()->numberBetween(1, 10),
        ];
    }
}
