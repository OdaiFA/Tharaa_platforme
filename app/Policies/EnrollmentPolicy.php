<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Enrollment $enrollment): bool
    {
        return $user->isAdmin() || $enrollment->user_id === $user->id;
    }

    public function create(User $user, Course $course): bool
    {
        return true;
    }

    public function update(User $user, Enrollment $enrollment): bool
    {
        return $enrollment->user_id === $user->id;
    }

    public function delete(User $user, Enrollment $enrollment): bool
    {
        return $enrollment->user_id === $user->id;
    }
}
