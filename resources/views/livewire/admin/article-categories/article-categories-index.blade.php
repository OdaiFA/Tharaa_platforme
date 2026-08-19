<div>
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">تصنيفات المقالات</h1>
        <p class="mt-1 text-sm text-gray-500">تنظيم المقالات التثقيفية حسب الموضوع</p>
    </div>

    <form wire:submit="save" class="mb-6 grid grid-cols-1 gap-3 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm md:grid-cols-3">
        <div>
            <input type="text" wire:model="name" placeholder="اسم التصنيف"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <input type="text" wire:model="slug" placeholder="الرابط المختصر (slug)"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
            @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <input type="text" wire:model="description" placeholder="وصف (اختياري)"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-2 md:col-span-3">
            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                class="flex-1 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-700 disabled:opacity-60">
                <span wire:loading.remove wire:target="save">{{ $editingId ? 'حفظ التعديل' : '+ إضافة' }}</span>
                <span wire:loading wire:target="save">جارٍ الحفظ...</span>
            </button>
            @if ($editingId)
                <button type="button" wire:click="cancelEdit"
                    class="rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-200">إلغاء</button>
            @endif
        </div>
    </form>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($categories as $category)
            <div wire:key="article-category-{{ $category->id }}" class="rounded-2xl border {{ $editingId === $category->id ? 'border-primary-400' : 'border-gray-100' }} bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-800">{{ $category->name }}</p>
                        <p class="text-xs text-gray-400">{{ $category->articles_count }} مقال · /{{ $category->slug }}</p>
                    </div>

                    @if ($confirmingDeleteId === $category->id)
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium text-red-600">حذف التصنيف؟</span>
                            <button type="button" wire:click="delete({{ $category->id }})" wire:loading.attr="disabled" wire:target="delete({{ $category->id }})"
                                class="rounded-lg bg-red-600 px-2.5 py-1 text-xs font-bold text-white hover:bg-red-700 disabled:opacity-60">نعم، احذف</button>
                            <button type="button" wire:click="cancelDelete"
                                class="rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-600 hover:bg-gray-200">إلغاء</button>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="edit({{ $category->id }})"
                                class="rounded-lg bg-gray-100 px-3 py-1 text-xs font-bold text-gray-600 hover:bg-gray-200">تعديل</button>
                            <button type="button" wire:click="confirmDelete({{ $category->id }})"
                                class="text-xs font-medium text-red-600 hover:underline">حذف</button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-400">لا توجد تصنيفات</p>
        @endforelse
    </div>
</div>
