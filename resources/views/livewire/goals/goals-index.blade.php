<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">أهداف الادخار</h1>
            <p class="mt-1 text-sm text-gray-500">حدد أهدافك المالية وتابع تحقيقها</p>
        </div>
        <a href="{{ route('goals.create') }}" class="btn-primary">+ هدف جديد</a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($goals as $goal)
            <div wire:key="goal-{{ $goal->id }}" class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gold-500/10 text-xl">{{ $goal->icon ?: '🎯' }}</span>
                        <div>
                            <h2 class="font-bold text-gray-900">{{ $goal->name }}</h2>
                            <p class="text-xs text-gray-400">
                                @if ($goal->deadline)
                                    الموعد النهائي: {{ $goal->deadline->translatedFormat('d M Y') }}
                                @else
                                    بدون موعد نهائي
                                @endif
                            </p>
                        </div>
                    </div>
                    @if ($goal->progress_percentage >= 100)
                        <span class="rounded-full bg-gold-50 px-2 py-0.5 text-xs font-bold text-gold-700">🏆 مكتمل</span>
                    @endif
                </div>

                <div class="mt-4 h-2 overflow-hidden rounded-full bg-gray-100">
                    <div class="h-full rounded-full {{ $goal->progress_percentage >= 100 ? 'bg-green-500' : 'bg-gold-gradient' }} transition-all"
                        style="width: {{ min($goal->progress_percentage, 100) }}%"></div>
                </div>
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-gray-500">
                        <b class="text-gray-800">{{ number_format($goal->current_amount, 2) }}</b> / {{ number_format($goal->target_amount, 2) }} {{ $goal->currency_code }}
                    </span>
                    <span class="font-bold text-gray-600">{{ $goal->progress_percentage }}%</span>
                </div>

                @if ($contributingGoalId === $goal->id)
                    <div class="mt-4 border-t border-gray-50 pt-3">
                        <p class="text-xs text-gray-500">المتبقي: {{ number_format(max($goal->target_amount - $goal->current_amount, 0), 2) }} {{ $goal->currency_code }}</p>

                        <label class="mt-3 mb-1.5 block text-sm font-medium text-gray-700">المبلغ</label>
                        <input type="number" wire:model="amount" step="0.01" min="0.01" autofocus class="input-field">
                        @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                        <label class="mt-3 mb-1.5 block text-sm font-medium text-gray-700">من الحساب</label>
                        <select wire:model="account_id" class="input-field">
                            <option value="">—</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->currency }})</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-400">عملة الهدف: {{ $goal->currency_code }} — يجب أن يكون الحساب بنفس العملة</p>
                        @error('account_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                        <label class="mt-3 mb-1.5 block text-sm font-medium text-gray-700">ملاحظة (اختياري)</label>
                        <input type="text" wire:model="note" maxlength="255" class="input-field">

                        <div class="mt-5 flex gap-2">
                            <button type="button" wire:click="contribute" wire:loading.attr="disabled" wire:target="contribute" class="btn-gold flex-1 disabled:opacity-60">إضافة</button>
                            <button type="button" wire:click="cancelContribute" class="btn-outline">إلغاء</button>
                        </div>
                    </div>
                @elseif ($confirmingDeleteId === $goal->id)
                    <div class="mt-4 flex items-center gap-2 border-t border-gray-50 pt-3">
                        <span class="flex-1 text-sm font-medium text-red-600">حذف هذا الهدف؟</span>
                        <button type="button" wire:click="delete({{ $goal->id }})" wire:loading.attr="disabled" wire:target="delete({{ $goal->id }})"
                            class="rounded-lg bg-red-600 px-3 py-2 text-sm font-bold text-white hover:bg-red-700 disabled:opacity-60">نعم</button>
                        <button type="button" wire:click="cancelDelete" class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50">إلغاء</button>
                    </div>
                @else
                    <div class="mt-4 flex items-center gap-2 border-t border-gray-50 pt-3">
                        <button type="button" wire:click="startContribute({{ $goal->id }})" class="btn-gold flex-1">إضافة مساهمة</button>
                        <a href="{{ route('goals.edit', $goal) }}" class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50">تعديل</a>
                        <button type="button" wire:click="confirmDelete({{ $goal->id }})" class="rounded-lg border border-red-100 px-3 py-2 text-sm text-red-600 hover:bg-red-50">حذف</button>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="text-gray-500">لا توجد أهداف بعد</p>
                <a href="{{ route('goals.create') }}" class="mt-3 inline-block text-sm font-bold text-primary-600 hover:underline">أنشئ هدفاً جديداً</a>
            </div>
        @endforelse
    </div>
</div>
