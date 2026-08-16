<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Course;
use App\Models\Goal;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\UserSetting;

class NotificationService
{
    /**
     * Send a notification respecting the user's channel settings (BR-NOT-001, 002).
     *
     * @param  array<int, string>  $channels
     */
    public function send(User $user, string $type, array $data, array $channels = ['in_app']): void
    {
        $settings = $user->settings;

        foreach ($channels as $channel) {
            if (! $this->channelAllowed($settings, $channel)) {
                continue;
            }

            if ($channel === 'in_app') {
                $this->storeInApp($user, $type, $data);
            }

            if ($channel === 'email') {
                // Email delivery is handled by dedicated Laravel notifications
                // (e.g. WelcomeNotification) to keep this service framework-agnostic.
            }
        }
    }

    public function sendBudgetAlert(User $user, Budget $budget): void
    {
        $this->send($user, 'budget_alert', [
            'title' => 'تنبيه ميزانية',
            'message' => "اقتربت ميزانية «{$budget->name}» من حدها المسموح",
            'action_url' => route('budgets.show', $budget),
        ], $user->settings->budget_alert_enabled ? ['in_app', 'email'] : ['email']);
    }

    public function sendGoalReminder(User $user, Goal $goal): void
    {
        $this->send($user, 'goal_reminder', [
            'title' => 'تذكير بهدفك',
            'message' => "اقترب موعد إنهاء هدفك «{$goal->name}» — المتبقي " . $goal->deadline->diffInDays(now()) . ' يوم',
            'action_url' => route('goals.index'),
        ], ['in_app']);
    }

    public function sendCourseReminder(User $user, Course $course): void
    {
        $this->send($user, 'course_reminder', [
            'title' => 'متابعة دورة',
            'message' => "لم تنتهِ بعد من دورة «{$course->title}» — واصل التعلّم",
            'action_url' => route('courses.learn', $course),
        ], ['in_app']);
    }

    private function channelAllowed(UserSetting $settings, string $channel): bool
    {
        if ($channel === 'in_app') {
            return true;
        }

        return $settings->channelEnabled($channel);
    }

    private function storeInApp(User $user, string $type, array $data): void
    {
        UserNotification::create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => $type,
            'title' => $data['title'] ?? '',
            'message' => $data['message'] ?? '',
            'data' => $data,
            'channel' => 'in_app',
            'is_read' => false,
            'action_url' => $data['action_url'] ?? null,
        ]);
    }
}
