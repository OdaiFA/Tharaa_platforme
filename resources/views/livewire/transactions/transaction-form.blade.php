<div class="mx-auto max-w-xl">
    <h1 class="text-2xl font-extrabold text-gray-900">{{ $transactionId ? 'تعديل المعاملة' : 'إضافة معاملة' }}</h1>
    @unless ($transactionId)
        <p class="mt-1 text-sm text-gray-500">سجل عملية مالية جديدة</p>
    @endunless

    <form wire:submit="save" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">نوع المعاملة</label>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" wire:click="setType('expense')"
                    class="rounded-xl border px-4 py-2.5 text-sm font-bold transition {{ $type === 'expense' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-200 bg-white text-gray-600' }}">مصروف</button>
                <button type="button" wire:click="setType('income')"
                    class="rounded-xl border px-4 py-2.5 text-sm font-bold transition {{ $type === 'income' ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 bg-white text-gray-600' }}">دخل</button>
                <button type="button" wire:click="setType('transfer')"
                    class="rounded-xl border px-4 py-2.5 text-sm font-bold transition {{ $type === 'transfer' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-600' }}">تحويل</button>
            </div>
            @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="account_id" class="mb-1.5 block text-sm font-medium text-gray-700">الحساب</label>
            <select id="account_id" wire:model="account_id"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                <option value="">—</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->currency }})</option>
                @endforeach
            </select>
            @error('account_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        @if ($type === 'transfer')
            <div>
                <label for="transfer_to_account_id" class="mb-1.5 block text-sm font-medium text-gray-700">الحساب المحوَّل إليه</label>
                <select id="transfer_to_account_id" wire:model="transfer_to_account_id"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="">—</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->currency }})</option>
                    @endforeach
                </select>
                @error('transfer_to_account_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        <div>
            <label for="amount" class="mb-1.5 block text-sm font-medium text-gray-700">المبلغ</label>
            <input id="amount" type="number" step="0.01" min="0.01" wire:model="amount"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        @if ($type !== 'transfer')
            <div>
                <label for="category_id" class="mb-1.5 block text-sm font-medium text-gray-700">التصنيف</label>
                <select id="category_id" wire:model="category_id"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="">—</option>
                    <optgroup label="المداخيل">
                        @foreach ($incomeCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="المصاريف">
                        @foreach ($expenseCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </optgroup>
                </select>
                @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        <div>
            <label for="description" class="mb-1.5 block text-sm font-medium text-gray-700">الوصف</label>
            <input id="description" type="text" wire:model="description" maxlength="255"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="transaction_date" class="mb-1.5 block text-sm font-medium text-gray-700">التاريخ</label>
            <input id="transaction_date" type="date" wire:model="transaction_date"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('transaction_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        @if ($transactionId)
            <div>
                <label for="recurrence_end_date" class="mb-1.5 block text-sm font-medium text-gray-700">نهاية التكرار (اختياري)</label>
                <input id="recurrence_end_date" type="date" wire:model="recurrence_end_date"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                @error('recurrence_end_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        @else
            <div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" wire:model.live="is_recurring" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    معاملة متكررة
                </label>
                @if ($is_recurring)
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <div>
                            <label for="recurrence_type" class="mb-1.5 block text-sm font-medium text-gray-700">التكرار</label>
                            <select id="recurrence_type" wire:model="recurrence_type" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                                <option value="daily">يومي</option>
                                <option value="weekly">أسبوعي</option>
                                <option value="monthly">شهري</option>
                                <option value="yearly">سنوي</option>
                            </select>
                            @error('recurrence_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="recurrence_end_date" class="mb-1.5 block text-sm font-medium text-gray-700">تاريخ النهاية</label>
                            <input id="recurrence_end_date" type="date" wire:model="recurrence_end_date" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <button type="submit" wire:loading.attr="disabled" wire:target="save"
            class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700 disabled:opacity-60">
            <span wire:loading.remove wire:target="save">{{ $transactionId ? 'حفظ التعديلات' : 'حفظ المعاملة' }}</span>
            <span wire:loading wire:target="save">جارٍ الحفظ...</span>
        </button>
    </form>
</div>
