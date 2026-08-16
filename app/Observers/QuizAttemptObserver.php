<?php

namespace App\Observers;

use App\Models\QuizAttempt;
use App\Services\EnrollmentService;

class QuizAttemptObserver
{
    public function __construct(private readonly EnrollmentService $service) {}

    public function created(QuizAttempt $attempt): void
    {
        if ($attempt->is_passed && $attempt->quiz && $attempt->quiz->lesson) {
            $this->service->completeLesson($attempt->user, $attempt->quiz->lesson);
        }
    }
}
