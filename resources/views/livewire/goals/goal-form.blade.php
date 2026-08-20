<div class="mx-auto max-w-xl">
    <h1 class="text-2xl font-extrabold text-gray-900">{{ $goalId ? 'تعديل الهدف' : 'إنشاء هدف جديد' }}</h1>
    @unless ($goalId)
        <p class="mt-1 text-sm text-gray-500">حدد ما تريد الادخار له</p>
    @endunless

    <form wire:submit="save" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">اسم الهدف</label>
            <input id="name" type="text" wire:model="name" autofocus placeholder="مثال: سيارة جديدة"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="target_amount" class="mb-1.5 block text-sm font-medium text-gray-700">المبلغ المستهدف</label>
                <input id="target_amount" type="number" step="0.01" min="1" wire:model="target_amount"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                @error('target_amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            @if ($goalId)
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">العملة</label>
                    <p class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500">{{ $currency_code }}</p>
                </div>
            @else
                <div>
                    <label for="currency_code" class="mb-1.5 block text-sm font-medium text-gray-700">العملة</label>
                    <input id="currency_code" type="text" wire:model="currency_code" maxlength="3"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm uppercase focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    @error('currency_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif
        </div>

        @unless ($goalId)
            <div>
                <label for="current_amount" class="mb-1.5 block text-sm font-medium text-gray-700">المبلغ الحالي (اختياري)</label>
                <input id="current_amount" type="number" step="0.01" min="0" wire:model="current_amount"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>
        @endunless

        <div>
            <label for="deadline" class="mb-1.5 block text-sm font-medium text-gray-700">{{ $goalId ? 'الموعد النهائي' : 'الموعد النهائي (اختياري)' }}</label>
            <input id="deadline" type="date" wire:model="deadline" @unless($goalId) min="{{ now()->format('Y-m-d') }}" @endunless
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('deadline') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="priority" class="mb-1.5 block text-sm font-medium text-gray-700">الأولوية</label>
            <select id="priority" wire:model="priority"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                <option value="low">منخفضة</option>
                <option value="medium">متوسطة</option>
                <option value="high">عالية</option>
            </select>
            @error('priority') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="icon" class="mb-1.5 block text-sm font-medium text-gray-700">{{ $goalId ? 'رمز تعبيري' : 'رمز تعبيري (اختياري)' }}</label>
            <input id="icon" type="text" wire:model="icon" maxlength="16" placeholder="🎯"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('icon') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="save"
            class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700 disabled:opacity-60">
            <span wire:loading.remove wire:target="save">{{ $goalId ? 'حفظ التعديلات' : 'إنشاء الهدف' }}</span>
            <span wire:loading wire:target="save">جارٍ الحفظ...</span>
        </button>
    </form>
</div>
