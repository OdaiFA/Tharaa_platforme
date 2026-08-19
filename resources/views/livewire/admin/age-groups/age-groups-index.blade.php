<div>
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">الفئات العمرية</h1>
        <p class="mt-1 text-sm text-gray-500">تُستخدم لتخصيص الدورات والمحتوى</p>
    </div>

    <form wire:submit="save" class="mb-6 grid grid-cols-1 gap-3 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm md:grid-cols-4">
        <div>
            <input type="text" wire:model="name" placeholder="اسم الفئة (مثال: أطفال 7-12)"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <input type="number" wire:model="min_age" placeholder="الحد الأدنى للعمر" min="0"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
            @error('min_age') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <input type="number" wire:model="max_age" placeholder="الحد الأقصى للعمر" min="0"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
            @error('max_age') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-2">
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
        @forelse ($ageGroups as $ageGroup)
            <div wire:key="age-group-{{ $ageGroup->id }}" class="rounded-2xl border {{ $editingId === $ageGroup->id ? 'border-primary-400' : 'border-gray-100' }} bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-800">{{ $ageGroup->name }}</p>
                        <p class="text-xs text-gray-400">{{ $ageGroup->min_age }} — {{ $ageGroup->max_age }} سنة · {{ $ageGroup->users_count }} مستخدم</p>
                    </div>
                    <button type="button" wire:click="edit({{ $ageGroup->id }})"
                        class="rounded-lg bg-gray-100 px-3 py-1 text-xs font-bold text-gray-600 hover:bg-gray-200">تعديل</button>
                </div>
            </div>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-400">لا توجد فئات عمرية</p>
        @endforelse
    </div>
</div>
