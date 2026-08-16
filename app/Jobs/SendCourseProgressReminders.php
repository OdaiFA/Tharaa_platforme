<?php

namespace App\Jobs;

use App\Models\Enrollment;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendCourseProgressReminders implements ShouldQueue
{
    use Queueable;

    public function handle(NotificationService $service): void
    {
        Enrollment::query()
            ->with(['user', 'course'])
            ->where('status', '!=', 'completed')
            ->where(function ($query) {
                $query->where('progress_percentage', '<', 100)
                    ->whereNull('completed_at');
            })
            ->each(function (Enrollment $enrollment) use ($service) {
                if ($enrollment->user->settings->course_reminder_enabled) {
                    $service->sendCourseReminder($enrollment->user, $enrollment->course);
                }
            });
    }
}
