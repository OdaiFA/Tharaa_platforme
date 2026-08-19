<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">إدارة الدورات</h1>
            <p class="mt-1 text-sm text-gray-500">أنشئ وتابع الدورات التعليمية</p>
        </div>
        <a href="{{ route('admin.courses.create') }}" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
            + دورة جديدة
        </a>
    </div>

    <div class="mb-6 flex flex-wrap gap-2">
        <input type="text" wire:model.live.debounce.400ms="search" placeholder="بحث..."
            class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-primary-500 focus:outline-none">
        <label class="flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm">
            <input type="checkbox" wire:model.live="published" class="h-4 w-4 text-primary-600"> منشورة فقط
        </label>
        <span wire:loading wire:target="search, published" class="flex items-center px-2 text-xs text-gray-400">جارٍ التحديث...</span>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50 text-right text-xs text-gray-500">
                    <th class="px-4 py-3 font-medium">العنوان</th>
                    <th class="px-4 py-3 font-medium">المستوى</th>
                    <th class="px-4 py-3 font-medium">الوحدات</th>
                    <th class="px-4 py-3 font-medium">المتعلمون</th>
                    <th class="px-4 py-3 font-medium">الحالة</th>
                    <th class="px-4 py-3 font-medium">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($courses as $course)
                    <tr wire:key="course-{{ $course->id }}" class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $course->title }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ match ($course->level) { 'beginner' => 'مبتدئ', 'intermediate' => 'متوسط', 'advanced' => 'متقدم', default => $course->level } }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $course->modules_count }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $course->enrollments_count }}</td>
                        <td class="px-4 py-3">
                            @if ($course->trashed())
                                <span class="rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-600">محذوفة</span>
                            @else
                                <span class="rounded-full {{ $course->is_published ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }} px-2.5 py-0.5 text-xs font-medium">
                                    {{ $course->is_published ? 'منشورة' : 'مسودة' }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if ($confirmingDeleteId === $course->id)
                                <span class="text-xs font-medium text-red-600">حذف الدورة؟</span>
                                <button type="button" wire:click="delete({{ $course->id }})" wire:loading.attr="disabled" wire:target="delete({{ $course->id }})"
                                    class="mr-1 rounded-lg bg-red-600 px-2 py-1 text-xs font-bold text-white hover:bg-red-700 disabled:opacity-60">نعم، احذف</button>
                                <button type="button" wire:click="cancelDelete"
                                    class="mr-1 rounded-lg bg-gray-100 px-2 py-1 text-xs font-bold text-gray-600 hover:bg-gray-200">إلغاء</button>
                            @elseif ($confirmingRestoreId === $course->id)
                                <span class="text-xs font-medium text-green-600">استعادة الدورة؟</span>
                                <button type="button" wire:click="restore({{ $course->id }})" wire:loading.attr="disabled" wire:target="restore({{ $course->id }})"
                                    class="mr-1 rounded-lg bg-green-600 px-2 py-1 text-xs font-bold text-white hover:bg-green-700 disabled:opacity-60">نعم، استعد</button>
                                <button type="button" wire:click="cancelRestore"
                                    class="mr-1 rounded-lg bg-gray-100 px-2 py-1 text-xs font-bold text-gray-600 hover:bg-gray-200">إلغاء</button>
                            @else
                                <a href="{{ route('admin.modules.index', $course) }}" class="text-primary-600 hover:underline">الوحدات</a>
                                <a href="{{ route('admin.courses.edit', $course) }}" class="mr-2 text-amber-600 hover:underline">تعديل</a>
                                <button type="button" wire:click="confirmDelete({{ $course->id }})" class="mr-2 text-red-600 hover:underline">حذف</button>
                                @if ($course->trashed())
                                    <button type="button" wire:click="confirmRestore({{ $course->id }})" class="mr-2 text-green-600 hover:underline">استعادة</button>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">لا توجد دورات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $courses->links() }}</div>
</div>
