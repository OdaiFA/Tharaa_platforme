<?php

namespace App\Listeners;

use App\Events\CourseCompleted;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\NotificationService;

class SendCongratulationNotification
{
    public function __construct(private readonly NotificationService $service) {}

    /**
     * Idempotent: CourseCompleted can legitimately be re-dispatched for an
     * enrollment that was already completed (e.g. re-checking certificate
     * eligibility after a later quiz pass) — this must not send a second
     * "you completed the course" notification.
     *
     * The dedup key is enrollment_id stored in the notification's `data`
     * payload — not the rendered action_url, which is not a stable key
     * (its host/path prefix depends on the request context that generated
     * it, e.g. a queued/CLI-triggered re-check vs. a web request).
     */
    public function handle(CourseCompleted $event): void
    {
        $enrollment = $event->enrollment;

        $alreadySent = UserNotification::query()
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $enrollment->user_id)
            ->where('type', 'course_completed')
            ->whereJsonContains('data->enrollment_id', $enrollment->id)
            ->exists();

        if ($alreadySent) {
            return;
        }

        $this->service->send($enrollment->user, 'course_completed', [
            'title' => 'أكملت الدورة 🎉',
            'message' => "تهانينا! لقد أكملت دورة «{$enrollment->course->title}» بنجاح.",
            'action_url' => route('courses.show', $enrollment->course),
            'enrollment_id' => $enrollment->id,
        ], ['in_app']);
    }
}
