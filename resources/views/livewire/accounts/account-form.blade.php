<div class="mx-auto max-w-xl">
    <h1 class="text-2xl font-extrabold text-gray-900">{{ $accountId ? 'تعديل الحساب' : 'إضافة حساب جديد' }}</h1>
    @unless ($accountId)
        <p class="mt-1 text-sm text-gray-500">أنشئ حساباً لمتابعة أموالك</p>
    @endunless

    <form wire:submit="save" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">اسم الحساب</label>
            <input id="name" type="text" wire:model="name" autofocus
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="type" class="mb-1.5 block text-sm font-medium text-gray-700">نوع الحساب</label>
            <select id="type" wire:model="type"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                <option value="bank">حساب بنكي</option>
                <option value="cash">نقدي</option>
                <option value="electronic">محفظة إلكترونية</option>
            </select>
            @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        @unless ($accountId)
            <div>
                <label for="initial_balance" class="mb-1.5 block text-sm font-medium text-gray-700">الرصيد الافتتاحي</label>
                <input id="initial_balance" type="number" step="0.01" wire:model="initial_balance"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                @error('initial_balance') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        @endunless

        <div>
            <label for="currency" class="mb-1.5 block text-sm font-medium text-gray-700">العملة</label>
            <input id="currency" type="text" wire:model="currency" maxlength="3"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('currency') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="mb-1.5 block text-sm font-medium text-gray-700">وصف (اختياري)</label>
            <textarea id="description" wire:model="description" rows="3"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"></textarea>
            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" wire:model="is_active" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            حساب نشط
        </label>

        <button type="submit" wire:loading.attr="disabled" wire:target="save"
            class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700 disabled:opacity-60">
            <span wire:loading.remove wire:target="save">{{ $accountId ? 'حفظ التعديلات' : 'إنشاء الحساب' }}</span>
            <span wire:loading wire:target="save">جارٍ الحفظ...</span>
        </button>
    </form>
</div>
