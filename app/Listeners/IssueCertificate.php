<?php

namespace App\Listeners;

use App\Events\CourseCompleted;
use App\Services\EnrollmentService;

class IssueCertificate
{
    public function __construct(private readonly EnrollmentService $service) {}

    public function handle(CourseCompleted $event): void
    {
        $this->service->issueCertificate($event->enrollment->fresh(['user', 'course']));
    }
}
