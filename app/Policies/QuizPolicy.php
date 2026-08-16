<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;

class QuizPolicy
{
    public function take(User $user, Quiz $quiz): bool
    {
        if (! $quiz->is_published || ! $quiz->lesson) {
            return false;
        }

        return $user->enrollments()->where('course_id', $quiz->lesson->module->course_id)->exists();
    }
}
