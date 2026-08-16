<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_channels',
        'language',
        'theme',
        'default_currency',
        'budget_alert_enabled',
        'goal_reminder_enabled',
        'course_reminder_enabled',
        'reminder_time',
    ];

    protected function casts(): array
    {
        return [
            'notification_channels' => 'array',
            'budget_alert_enabled' => 'boolean',
            'goal_reminder_enabled' => 'boolean',
            'course_reminder_enabled' => 'boolean',
            'reminder_time' => 'string',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function channelEnabled(string $channel): bool
    {
        $channels = $this->notification_channels ?? ['in_app'];

        return in_array($channel, $channels, true);
    }
}
