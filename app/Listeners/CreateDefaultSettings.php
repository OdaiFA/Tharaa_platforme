<?php

namespace App\Listeners;

use App\Events\UserRegistered;

class CreateDefaultSettings
{
    public function handle(UserRegistered $event): void
    {
        if (! $event->user->settings()->exists()) {
            $event->user->settings()->create([
                'notification_channels' => ['in_app'],
                'language' => 'ar',
                'theme' => 'light',
                'default_currency' => $event->user->currency,
                'reminder_time' => '20:00:00',
            ]);
        }
    }
}
