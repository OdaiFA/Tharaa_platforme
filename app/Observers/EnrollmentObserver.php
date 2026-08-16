<?php

namespace App\Observers;

use App\Models\Enrollment;
use App\Services\NotificationService;

class EnrollmentObserver
{
    public function __construct(private readonly NotificationService $service) {}

    public function created(Enrollment $enrollment): void
    {
        $this->service->send($enrollment->user, 'enrollment_welcome', [
            'title' => 'تم التسجيل في الدورة 🎓',
            'message' => "بدأت رحلتك في دورة «{$enrollment->course->title}». بالتوفيق!",
            'action_url' => route('courses.learn', $enrollment->course),
        ], ['in_app']);
    }
}
