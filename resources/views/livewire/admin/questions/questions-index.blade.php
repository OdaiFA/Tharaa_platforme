<div>
    <div class="mb-6">
        <a href="{{ route('admin.quizzes.index', $quiz->lesson) }}" class="text-sm font-medium text-primary-600 hover:underline">← الاختبار</a>
        <h1 class="mt-1 text-2xl font-extrabold text-gray-900">أسئلة اختبار «{{ $quiz->title }}»</h1>
    </div>

    <div class="mb-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <form wire:submit="save" class="space-y-3">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">نص السؤال</label>
                <textarea wire:model="question" rows="2" placeholder="اكتب السؤال..."
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none"></textarea>
                @error('question') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700">نوع السؤال</label>
                <select wire:model.live="type" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-primary-500 focus:outline-none">
                    <option value="multiple_choice">اختيار من متعدد</option>
                    <option value="true_false">صحيح / خطأ</option>
                </select>
            </div>

            @if ($type === 'true_false')
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">الإجابة الصحيحة</label>
                    <select wire:model="true_false_answer" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-primary-500 focus:outline-none">
                        <option value="true">صحيح</option>
                        <option value="false">خطأ</option>
                    </select>
                </div>
            @else
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">الخيارات (من 2 إلى 6)</label>
                    <div class="space-y-2">
                        @foreach ($options as $index => $option)
                            <div class="flex items-center gap-2" wire:key="option-{{ $index }}">
                                <input type="text" wire:model="options.{{ $index }}" placeholder="الخيار {{ $index + 1 }}"
                                    class="flex-1 rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-primary-500 focus:outline-none">
                                <label class="flex items-center gap-1 text-xs text-gray-500">
                                    <input type="checkbox" wire:model="correct_answer" value="{{ $index }}" class="h-4 w-4 text-primary-600 focus:ring-primary-500"> صحيح
                                </label>
                                @if (count($options) > 2)
                                    <button type="button" wire:click="removeOption({{ $index }})" class="text-xs text-red-500 hover:underline">إزالة</button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @error('options') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    @error('correct_answer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    @if (count($options) < 6)
                        <button type="button" wire:click="addOption" class="mt-2 text-xs font-bold text-primary-600 hover:underline">+ إضافة خيار</button>
                    @endif
                </div>
            @endif

            <div class="flex items-center gap-3 pt-2">
                <label class="text-sm font-medium text-gray-700">النقاط</label>
                <input type="number" wire:model="points" min="1" class="w-24 rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-primary-500 focus:outline-none">
                <button type="submit" class="mr-auto rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-primary-700">
                    {{ $editingId ? 'حفظ التعديلات' : '+ إضافة السؤال' }}
                </button>
                @if ($editingId)
                    <button type="button" wire:click="cancelEdit" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50">إلغاء</button>
                @endif
            </div>
        </form>
    </div>

    <div class="space-y-3">
        @forelse ($quiz->questions as $questionRow)
            <div wire:key="question-row-{{ $questionRow->id }}" class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-bold text-gray-900">{{ $questionRow->question }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $questionRow->type === 'true_false' ? 'صحيح/خطأ' : 'اختيار من متعدد' }} · {{ $questionRow->points }} نقطة</p>
                        @if ($questionRow->options)
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($questionRow->options as $index => $option)
                                    <span class="rounded-lg bg-gray-50 px-2.5 py-1 text-xs {{ in_array((string) $index, array_map('strval', $questionRow->correct_answer ?? [])) ? 'bg-green-50 font-bold text-green-700' : 'text-gray-600' }}">
                                        {{ $option }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="flex shrink-0 items-center gap-2 text-sm">
                        <button type="button" wire:click="edit({{ $questionRow->id }})" class="text-primary-600 hover:underline">تعديل</button>
                        @if ($confirmingDeleteId === $questionRow->id)
                            <button type="button" wire:click="delete({{ $questionRow->id }})" class="text-red-600 hover:underline">تأكيد الحذف</button>
                            <button type="button" wire:click="cancelDelete" class="text-gray-500 hover:underline">إلغاء</button>
                        @else
                            <button type="button" wire:click="confirmDelete({{ $questionRow->id }})" class="text-red-600 hover:underline">حذف</button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-400">لا توجد أسئلة بعد</p>
        @endforelse
    </div>
</div>
