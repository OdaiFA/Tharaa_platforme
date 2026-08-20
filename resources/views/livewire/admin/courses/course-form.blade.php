<div class="mx-auto max-w-2xl">
    <h1 class="text-2xl font-extrabold text-gray-900">{{ $courseId ? 'تعديل الدورة' : 'إنشاء دورة جديدة' }}</h1>

    <form wire:submit="save" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div>
            <label for="title" class="mb-1.5 block text-sm font-medium text-gray-700">عنوان الدورة</label>
            <input id="title" type="text" wire:model="title"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="mb-1.5 block text-sm font-medium text-gray-700">الوصف</label>
            <textarea id="description" wire:model="description" rows="4"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="level" class="mb-1.5 block text-sm font-medium text-gray-700">المستوى</label>
                <select id="level" wire:model="level" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="beginner">مبتدئ</option>
                    <option value="intermediate">متوسط</option>
                    <option value="advanced">متقدم</option>
                </select>
                @error('level') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="duration_hours" class="mb-1.5 block text-sm font-medium text-gray-700">المدة بالساعات</label>
                <input id="duration_hours" type="number" min="0" wire:model="duration_hours"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>
            <div>
                <label for="passing_score" class="mb-1.5 block text-sm font-medium text-gray-700">درجة النجاح (%)</label>
                <input id="passing_score" type="number" min="0" max="100" wire:model="passing_score"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>
            <div>
                <label for="age_groups" class="mb-1.5 block text-sm font-medium text-gray-700">الفئات العمرية</label>
                <select id="age_groups" wire:model="age_groups" multiple
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    @foreach ($ageGroups as $ageGroup)
                        <option value="{{ $ageGroup->id }}">{{ $ageGroup->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="thumbnail" class="mb-1.5 block text-sm font-medium text-gray-700">الصورة المصغرة</label>
                <input id="thumbnail" type="file" wire:model="thumbnail" accept="image/*"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-bold file:text-primary-700">
                @error('thumbnail') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                @if ($existingThumbnail && ! $thumbnail)
                    <p class="mt-1 text-xs text-gray-400">الصورة الحالية محفوظة، اختر ملفاً جديداً لاستبدالها</p>
                @endif
            </div>
        </div>

        <div class="space-y-2">
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model="is_published" class="h-4 w-4 text-primary-600"> نشر الدورة
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model="certificate_enabled" class="h-4 w-4 text-primary-600"> تفعيل شهادة الإتمام
            </label>
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="save,thumbnail"
            class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white hover:bg-primary-700 disabled:opacity-60">
            <span wire:loading.remove wire:target="save">{{ $courseId ? 'حفظ التعديلات' : 'إنشاء الدورة' }}</span>
            <span wire:loading wire:target="save">جارٍ الحفظ...</span>
        </button>
    </form>
</div>
