<?php

namespace App\Listeners;

use App\Events\LessonCompleted;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\EnrollmentService;

class CheckCourseCompletion
{
    public function __construct(private readonly EnrollmentService $service) {}

    public function handle(LessonCompleted $event): void
    {
        $enrollment = Enrollment::with('course')
            ->where('user_id', $event->user->id)
            ->where('course_id', $event->lesson->module->course_id)
            ->first();

        if ($enrollment) {
            $this->service->checkCourseCompletion($enrollment);
        }
    }
}
