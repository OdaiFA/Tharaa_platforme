<?php

namespace App\Livewire\Settings;

use Illuminate\Validation\Rule;
use Livewire\Component;

class SettingsForm extends Component
{
    public array $notification_channels = [];

    public string $language = 'ar';

    public string $theme = 'light';

    public ?string $default_currency = null;

    public bool $budget_alert_enabled = true;

    public bool $goal_reminder_enabled = true;

    public bool $course_reminder_enabled = true;

    public string $reminder_time = '20:00';

    public function mount(): void
    {
        $user = auth()->user();
        $settings = $user->settings()->firstOrNew();

        $this->notification_channels = $settings->notification_channels ?? [];
        $this->language = $settings->language ?? 'ar';
        $this->theme = $settings->theme ?? 'light';
        $this->default_currency = $settings->default_currency ?? $user->currency;
        $this->budget_alert_enabled = $settings->budget_alert_enabled ?? true;
        $this->goal_reminder_enabled = $settings->goal_reminder_enabled ?? true;
        $this->course_reminder_enabled = $settings->course_reminder_enabled ?? true;
        $this->reminder_time = $settings->reminder_time ? substr($settings->reminder_time, 0, 5) : '20:00';
    }

    protected function rules(): array
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

    protected function messages(): array
    {
        return [
            'notification_channels.*' => 'قناة الإشعار غير صالحة',
            'theme.in' => 'المظهر غير صالح',
            'default_currency.size' => 'رمز العملة يجب أن يكون 3 أحرف',
            'reminder_time.date_format' => 'صيغة وقت التذكير غير صحيحة',
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();
        $user = auth()->user();

        if ($user->settings()->exists()) {
            $user->settings()->update($validated);
        } else {
            $user->settings()->create(array_merge($validated, [
                'user_id' => $user->id,
            ]));
        }

        session()->flash('success', 'تم تحديث الإعدادات بنجاح');
    }

    public function render()
    {
        return view('livewire.settings.settings-form');
    }
}
