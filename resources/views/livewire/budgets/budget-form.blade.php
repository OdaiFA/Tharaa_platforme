<div class="mx-auto max-w-2xl">
    <h1 class="text-2xl font-extrabold text-gray-900">{{ $budgetId ? 'تعديل الميزانية' : 'إنشاء ميزانية' }}</h1>
    @unless ($budgetId)
        <p class="mt-1 text-sm text-gray-500">حدد الحدود الشهرية لإنفاقك حسب التصنيفات</p>
    @endunless

    <form wire:submit="save" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">اسم الميزانية</label>
                <input id="name" type="text" wire:model="name"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="total_amount" class="mb-1.5 block text-sm font-medium text-gray-700">الإجمالي</label>
                <input id="total_amount" type="number" step="0.01" min="1" wire:model="total_amount"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                @error('total_amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="start_date" class="mb-1.5 block text-sm font-medium text-gray-700">تاريخ البداية</label>
                <input id="start_date" type="date" wire:model="start_date"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                @error('start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="end_date" class="mb-1.5 block text-sm font-medium text-gray-700">تاريخ النهاية</label>
                <input id="end_date" type="date" wire:model="end_date"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                @error('end_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="alert_percentage" class="mb-1.5 block text-sm font-medium text-gray-700">نسبة التنبيه الافتراضية (%)</label>
                <input id="alert_percentage" type="number" min="1" max="100" wire:model="alert_percentage"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                @error('alert_percentage') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            @if ($budgetId)
                <div>
                    <label class="mb-1.5 flex items-center gap-2 pt-7 text-sm font-medium text-gray-700">
                        <input type="checkbox" wire:model="is_active" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        ميزانية نشطة
                    </label>
                </div>
            @else
                <div>
                    <label for="currency" class="mb-1.5 block text-sm font-medium text-gray-700">العملة</label>
                    <input id="currency" type="text" wire:model="currency" maxlength="3"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    @error('currency') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif
        </div>

        @unless ($budgetId)
            <div class="rounded-xl border border-dashed border-gray-200 p-4">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-700">حدود التصنيفات (اختياري)</h2>
                    <button type="button" wire:click="addCategoryRow" class="rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-bold text-primary-700 hover:bg-primary-100">
                        + إضافة تصنيف
                    </button>
                </div>

                @error('categories') <p class="mb-2 text-xs text-red-600">{{ $message }}</p> @enderror

                @foreach ($categories as $index => $row)
                    <div wire:key="category-row-{{ $index }}" class="mb-3 grid grid-cols-12 gap-2 rounded-lg bg-gray-50 p-3">
                        <select wire:model="categories.{{ $index }}.category_id"
                            class="col-span-12 rounded-lg border border-gray-300 px-3 py-2 text-sm sm:col-span-5 focus:border-primary-500 focus:outline-none">
                            <option value="">اختر التصنيف</option>
                            @foreach ($expenseCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <input wire:model="categories.{{ $index }}.limit_amount" type="number" step="0.01" min="1" placeholder="الحد الأقصى"
                            class="col-span-6 rounded-lg border border-gray-300 px-3 py-2 text-sm sm:col-span-4 focus:border-primary-500 focus:outline-none">
                        <input wire:model="categories.{{ $index }}.alert_percentage" type="number" min="1" max="100" placeholder="نسبة التنبيه"
                            class="col-span-6 rounded-lg border border-gray-300 px-3 py-2 text-sm sm:col-span-2 focus:border-primary-500 focus:outline-none">
                        <button type="button" wire:click="removeCategoryRow({{ $index }})" class="col-span-12 text-right text-xs font-bold text-red-500 hover:underline sm:col-span-1">
                            إزالة
                        </button>
                        @error("categories.{$index}.category_id") <p class="col-span-12 text-xs text-red-600">{{ $message }}</p> @enderror
                        @error("categories.{$index}.limit_amount") <p class="col-span-12 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endforeach

                @if (count($categories) === 0)
                    <p class="text-center text-xs text-gray-400">لا توجد تصنيفات مضافة — حدد حدوداً لكل تصنيف إن أردت</p>
                @endif
            </div>
        @endunless

        <button type="submit" wire:loading.attr="disabled" wire:target="save"
            class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700 disabled:opacity-60">
            <span wire:loading.remove wire:target="save">{{ $budgetId ? 'حفظ التعديلات' : 'إنشاء الميزانية' }}</span>
            <span wire:loading wire:target="save">جارٍ الحفظ...</span>
        </button>
    </form>
</div>
