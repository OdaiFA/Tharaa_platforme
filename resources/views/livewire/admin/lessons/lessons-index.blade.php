<div>
    <div class="mb-6">
        <a href="{{ route('admin.courses.index') }}" class="text-sm font-medium text-primary-600 hover:underline">← كل الدورات</a>
        <h1 class="mt-1 text-2xl font-extrabold text-gray-900">دروس الوحدة «{{ $module->title }}»</h1>
    </div>

    <form wire:submit="save" class="mb-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <div class="space-y-3">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <div>
                    <input type="text" wire:model="title" placeholder="عنوان الدرس"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <input type="url" wire:model="video_url" placeholder="رابط الفيديو (اختياري)"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                    @error('video_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex gap-2">
                    <input type="number" wire:model="duration_minutes" placeholder="المدة (دقائق)" min="0" class="w-28 rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                    <button type="submit" class="flex-1 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-700">
                        {{ $editingId ? 'حفظ' : '+ إضافة' }}
                    </button>
                    @if ($editingId)
                        <button type="button" wire:click="cancelEdit" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50">إلغاء</button>
                    @endif
                </div>
            </div>
            <textarea wire:model="content" rows="3" placeholder="محتوى الدرس (اختياري)"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none"></textarea>
        </div>
    </form>

    <div class="space-y-4">
        @forelse ($module->lessons as $lesson)
            <div wire:key="lesson-{{ $lesson->id }}" class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-gray-900">{{ $lesson->title }}</h2>
                        <p class="mt-0.5 line-clamp-1 text-sm text-gray-500">{{ $lesson->content ?: ($lesson->video_url ?: 'لا يوجد محتوى') }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $lesson->duration_minutes }} دقيقة</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.quizzes.index', $lesson) }}" class="rounded-lg bg-purple-50 px-3 py-1.5 text-xs font-bold text-purple-700 hover:bg-purple-100">الاختبار</a>
                        <button type="button" wire:click="edit({{ $lesson->id }})" class="rounded-lg bg-gray-50 px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-100">تعديل</button>
                        @if ($confirmingDeleteId === $lesson->id)
                            <button type="button" wire:click="delete({{ $lesson->id }})" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-red-700">تأكيد الحذف</button>
                            <button type="button" wire:click="cancelDelete" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50">إلغاء</button>
                        @else
                            <button type="button" wire:click="confirmDelete({{ $lesson->id }})" class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100">حذف</button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="text-gray-400">لا توجد دروس بعد</p>
            </div>
        @endforelse
    </div>
</div>
