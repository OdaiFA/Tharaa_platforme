@extends('layouts.app')

@section('title', 'الإعدادات')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-2xl font-extrabold text-gray-900">الإعدادات</h1>
        <p class="mt-1 text-sm text-gray-500">خصص تجربتك مع المنصة</p>

        <form method="POST" action="{{ route('settings.update') }}" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">قنوات الإشعارات</label>
                <div class="space-y-2">
                    @foreach (['push' => 'دفع', 'email' => 'بريد إلكتروني', 'in_app' => 'داخل التطبيق'] as $value => $label)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="notification_channels[]" value="{{ $value }}"
                                @checked(in_array($value, $settings->notification_channels ?? []))
                                class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="language" class="mb-1.5 block text-sm font-medium text-gray-700">اللغة</label>
                    <input id="language" type="text" name="language" value="{{ old('language', $settings->language ?? 'ar') }}" maxlength="5"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="theme" class="mb-1.5 block text-sm font-medium text-gray-700">المظهر</label>
                    <select id="theme" name="theme" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        <option value="light" @selected(($settings->theme ?? 'light') === 'light')>فاتح</option>
                        <option value="dark" @selected(($settings->theme ?? 'light') === 'dark')>داكن</option>
                    </select>
                </div>
                <div>
                    <label for="default_currency" class="mb-1.5 block text-sm font-medium text-gray-700">العملة الافتراضية</label>
                    <input id="default_currency" type="text" name="default_currency" value="{{ old('default_currency', $settings->default_currency ?? auth()->user()->currency) }}" maxlength="3"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="reminder_time" class="mb-1.5 block text-sm font-medium text-gray-700">وقت التذكيرات</label>
                    <input id="reminder_time" type="time" name="reminder_time" value="{{ old('reminder_time', $settings->reminder_time ?? '20:00') }}"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
            </div>

            <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="budget_alert_enabled" value="1" @checked($settings->budget_alert_enabled ?? true)
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    تفعيل تنبيهات الميزانيات
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="goal_reminder_enabled" value="1" @checked($settings->goal_reminder_enabled ?? true)
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    تفعيل تذكيرات الأهداف
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="course_reminder_enabled" value="1" @checked($settings->course_reminder_enabled ?? true)
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    تفعيل تذكيرات الدورات
                </label>
            </div>

            <button type="submit" class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                حفظ الإعدادات
            </button>
        </form>
    </div>
@endsection
