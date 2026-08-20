<div>
    <div class="mb-6">
        <a href="{{ route('admin.courses.index') }}" class="text-sm font-medium text-primary-600 hover:underline">← كل الدورات</a>
        <h1 class="mt-1 text-2xl font-extrabold text-gray-900">وحدات دورة «{{ $course->title }}»</h1>
    </div>

    <form wire:submit="save" class="mb-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <div>
                <input type="text" wire:model="title" placeholder="عنوان الوحدة"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <input type="text" wire:model="description" placeholder="وصف الوحدة (اختياري)"
                class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
            <div class="flex gap-2">
                <input type="number" wire:model="order_index" placeholder="الترتيب" min="0" class="w-24 rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                <button type="submit" class="flex-1 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-700">
                    {{ $editingId ? 'حفظ التعديلات' : '+ إضافة' }}
                </button>
                @if ($editingId)
                    <button type="button" wire:click="cancelEdit" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50">إلغاء</button>
                @endif
            </div>
        </div>
    </form>

    <div class="space-y-4">
        @forelse ($course->modules as $module)
            <div wire:key="module-{{ $module->id }}" class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-gray-900">{{ $module->order_index ?: '' }} {{ $module->title }}</h2>
                        <p class="mt-0.5 text-sm text-gray-500">{{ $module->description }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $module->lessons_count }} درس</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.lessons.index', $module) }}" class="rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-bold text-primary-700 hover:bg-primary-100">الدروس</a>
                        <button type="button" wire:click="edit({{ $module->id }})" class="rounded-lg bg-gray-50 px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-100">تعديل</button>
                        @if ($confirmingDeleteId === $module->id)
                            <button type="button" wire:click="delete({{ $module->id }})" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-red-700">تأكيد الحذف</button>
                            <button type="button" wire:click="cancelDelete" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50">إلغاء</button>
                        @else
                            <button type="button" wire:click="confirmDelete({{ $module->id }})" class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100">حذف</button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="text-gray-400">لا توجد وحدات بعد</p>
            </div>
        @endforelse
    </div>
</div>
