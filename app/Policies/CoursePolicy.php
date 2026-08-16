<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Course $course): bool
    {
        return $user->isAdmin() || $course->is_published;
    }

    public function learn(User $user, Course $course): bool
    {
        return $user->enrollments()->where('course_id', $course->id)->exists();
    }

    public function enroll(User $user, Course $course): bool
    {
        return $course->is_published && $user->id !== $course->created_by;
    }

    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
}
