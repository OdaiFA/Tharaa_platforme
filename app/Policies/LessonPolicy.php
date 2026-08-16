<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    public function view(User $user, Lesson $lesson): bool
    {
        return $user->isAdmin() || $lesson->is_published;
    }

    public function complete(User $user, Lesson $lesson): bool
    {
        return $user->enrollments()->where('course_id', $lesson->module->course_id)->exists();
    }
}
