<?php

namespace Database\Factories;

use App\Models\AgeGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $ageGroup = AgeGroup::query()->inRandomOrder()->first();

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'date_of_birth' => fake()->dateTimeBetween('-60 years', '-7 years')->format('Y-m-d'),
            'age_group_id' => $ageGroup?->id,
            'currency' => 'SAR',
            'financial_level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'role' => 'user',
            'is_active' => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
