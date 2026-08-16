<?php

namespace App\Listeners;

use App\Events\CourseCompleted;
use App\Services\NotificationService;

class SendCongratulationNotification
{
    public function __construct(private readonly NotificationService $service) {}

    public function handle(CourseCompleted $event): void
    {
        $enrollment = $event->enrollment;

        $this->service->send($enrollment->user, 'course_completed', [
            'title' => 'أكملت الدورة 🎉',
            'message' => "تهانينا! لقد أكملت دورة «{$enrollment->course->title}» بنجاح.",
            'action_url' => route('courses.show', $enrollment->course),
        ], ['in_app']);
    }
}
