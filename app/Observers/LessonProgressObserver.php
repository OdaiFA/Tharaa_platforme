<?php

namespace App\Observers;

use App\Models\LessonProgress;
use App\Services\EnrollmentService;

class LessonProgressObserver
{
    public function __construct(private readonly EnrollmentService $service) {}

    public function created(LessonProgress $progress): void
    {
        $this->handle($progress);
    }

    public function updated(LessonProgress $progress): void
    {
        $this->handle($progress);
    }

    private function handle(LessonProgress $progress): void
    {
        if ($progress->status === 'completed' && $progress->enrollment) {
            $this->service->updateProgress($progress->enrollment->fresh());
        }
    }
}
