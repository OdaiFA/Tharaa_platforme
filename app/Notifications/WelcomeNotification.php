<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly User $user) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('أهلاً بك في ثراء 🌟')
            ->greeting("أهلاً {$this->user->name}،")
            ->line('مرحباً بك في منصة ثراء للتعليم المالي.')
            ->line('ابدأ رحلتك في تعلم إدارة أموالك بذكاء.')
            ->action('استكشف المنصة', url('/'))
            ->line('شكراً لانضمامك إلينا!');
    }
}
