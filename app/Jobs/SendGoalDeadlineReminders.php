<?php

namespace App\Jobs;

use App\Models\Goal;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendGoalDeadlineReminders implements ShouldQueue
{
    use Queueable;

    public function handle(NotificationService $service): void
    {
        Goal::query()
            ->active()
            ->whereBetween('deadline', [now(), now()->addDays(7)])
            ->with('user')
            ->each(function (Goal $goal) use ($service) {
                if ($goal->user->settings->goal_reminder_enabled) {
                    $service->sendGoalReminder($goal->user, $goal);
                }
            });
    }
}
