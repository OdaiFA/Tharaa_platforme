<div>
    <div class="mb-6">
        <a href="{{ route('admin.lessons.index', $lesson->module) }}" class="text-sm font-medium text-primary-600 hover:underline">← دروس الوحدة</a>
        <h1 class="mt-1 text-2xl font-extrabold text-gray-900">اختبار درس «{{ $lesson->title }}»</h1>
    </div>

    <form wire:submit="save" class="mb-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <div class="md:col-span-2">
                <input type="text" wire:model="title" placeholder="عنوان الاختبار"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <input type="number" wire:model="passing_score" placeholder="درجة النجاح %" min="0" max="100"
                class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
            <input type="number" wire:model="max_attempts" placeholder="عدد المحاولات" min="1" max="10"
                class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
        </div>
        <div class="mt-3 flex items-center gap-4">
            <input type="text" wire:model="description" placeholder="وصف الاختبار (اختياري)"
                class="flex-1 rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model="is_published" class="h-4 w-4 text-primary-600"> نشر
            </label>
            <button type="submit" class="rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-primary-700">حفظ</button>
        </div>
    </form>

    @if ($lesson->quiz)
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">أسئلة الاختبار ({{ $lesson->quiz->questions->count() }})</h2>
            <a href="{{ route('admin.questions.index', $lesson->quiz) }}" class="rounded-lg bg-purple-50 px-3 py-1.5 text-xs font-bold text-purple-700 hover:bg-purple-100">
                إدارة الأسئلة
            </a>
        </div>
        <div class="space-y-2">
            @forelse ($lesson->quiz->questions as $question)
                <div wire:key="question-{{ $question->id }}" class="flex items-center justify-between rounded-xl border border-gray-100 bg-white p-4 text-sm">
                    <div>
                        <p class="font-medium text-gray-800">{{ $question->question }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $question->type === 'true_false' ? 'صحيح/خطأ' : 'اختيار من متعدد' }} · {{ $question->points }} نقطة
                        </p>
                    </div>
                    @if ($confirmingDeleteId === $question->id)
                        <span class="flex items-center gap-2">
                            <button type="button" wire:click="deleteQuestion({{ $question->id }})" class="text-red-600 hover:underline">تأكيد الحذف</button>
                            <button type="button" wire:click="cancelDeleteQuestion" class="text-gray-500 hover:underline">إلغاء</button>
                        </span>
                    @else
                        <button type="button" wire:click="confirmDeleteQuestion({{ $question->id }})" class="text-red-600 hover:underline">حذف</button>
                    @endif
                </div>
            @empty
                <p class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-400">لا توجد أسئلة بعد</p>
            @endforelse
        </div>
    @endif
</div>
