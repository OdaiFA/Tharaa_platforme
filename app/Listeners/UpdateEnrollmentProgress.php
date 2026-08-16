<?php

namespace App\Listeners;

use App\Events\LessonCompleted;
use App\Services\EnrollmentService;

class UpdateEnrollmentProgress
{
    public function __construct(private readonly EnrollmentService $service) {}

    public function handle(LessonCompleted $event): void
    {
        $this->service->updateProgress($event->enrollment->fresh());
    }
}
