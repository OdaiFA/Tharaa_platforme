<div class="mx-auto max-w-xl">
    <h1 class="text-2xl font-extrabold text-gray-900">الإعدادات</h1>
    <p class="mt-1 text-sm text-gray-500">خصص تجربتك مع المنصة</p>

    @if (session('success'))
        <div class="mt-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <form wire:submit="save" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">قنوات الإشعارات</label>
            <div class="space-y-2">
                @foreach (['push' => 'دفع', 'email' => 'بريد إلكتروني', 'in_app' => 'داخل التطبيق'] as $value => $label)
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" wire:model="notification_channels" value="{{ $value }}"
                            class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="language" class="mb-1.5 block text-sm font-medium text-gray-700">اللغة</label>
                <input id="language" type="text" wire:model="language" maxlength="5"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>
            <div>
                <label for="theme" class="mb-1.5 block text-sm font-medium text-gray-700">المظهر</label>
                <select id="theme" wire:model="theme" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="light">فاتح</option>
                    <option value="dark">داكن</option>
                </select>
            </div>
            <div>
                <label for="default_currency" class="mb-1.5 block text-sm font-medium text-gray-700">العملة الافتراضية</label>
                <input id="default_currency" type="text" wire:model="default_currency" maxlength="3"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                @error('default_currency') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="reminder_time" class="mb-1.5 block text-sm font-medium text-gray-700">وقت التذكيرات</label>
                <input id="reminder_time" type="time" wire:model="reminder_time"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>
        </div>

        <div class="space-y-2">
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model="budget_alert_enabled" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                تفعيل تنبيهات الميزانيات
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model="goal_reminder_enabled" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                تفعيل تذكيرات الأهداف
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model="course_reminder_enabled" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                تفعيل تذكيرات الدورات
            </label>
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="save"
            class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700 disabled:opacity-60">
            <span wire:loading.remove wire:target="save">حفظ الإعدادات</span>
            <span wire:loading wire:target="save">جارٍ الحفظ...</span>
        </button>
    </form>
</div>
