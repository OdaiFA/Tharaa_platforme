<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'status' => 'in_progress',
            'progress_percentage' => fake()->numberBetween(0, 100),
            'enrolled_at' => now(),
            'certificate_issued' => false,
        ];
    }
}
