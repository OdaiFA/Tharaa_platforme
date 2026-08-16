<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notification_channels' => ['nullable', 'array'],
            'notification_channels.*' => [Rule::in(['push', 'email', 'in_app'])],
            'language' => ['nullable', 'string', 'max:5'],
            'theme' => ['nullable', Rule::in(['light', 'dark'])],
            'default_currency' => ['nullable', 'string', 'size:3'],
            'budget_alert_enabled' => ['sometimes', 'boolean'],
            'goal_reminder_enabled' => ['sometimes', 'boolean'],
            'course_reminder_enabled' => ['sometimes', 'boolean'],
            'reminder_time' => ['nullable', 'date_format:H:i'],
        ];
    }

    public function messages(): array
    {
        return [
            'notification_channels.*' => 'قناة الإشعار غير صالحة',
            'theme.in' => 'المظهر غير صالح',
            'default_currency.size' => 'رمز العملة يجب أن يكون 3 أحرف',
            'reminder_time.date_format' => 'صيغة وقت التذكير غير صحيحة',
        ];
    }
}
