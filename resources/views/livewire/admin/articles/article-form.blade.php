<div class="mx-auto max-w-2xl">
    <h1 class="text-2xl font-extrabold text-gray-900">{{ $articleId ? 'تعديل المقال' : 'إنشاء مقال جديد' }}</h1>

    <form wire:submit="save" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div>
            <label for="title" class="mb-1.5 block text-sm font-medium text-gray-700">العنوان</label>
            <input id="title" type="text" wire:model="title"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="excerpt" class="mb-1.5 block text-sm font-medium text-gray-700">المقتطف (اختياري)</label>
            <textarea id="excerpt" wire:model="excerpt" rows="2" maxlength="500"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"></textarea>
        </div>

        <div>
            <label for="content" class="mb-1.5 block text-sm font-medium text-gray-700">المحتوى (يدعم Markdown)</label>
            <textarea id="content" wire:model="content" rows="10"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"></textarea>
            @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="category_id" class="mb-1.5 block text-sm font-medium text-gray-700">التصنيف</label>
                <select id="category_id" wire:model="category_id" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="">—</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="featured_image" class="mb-1.5 block text-sm font-medium text-gray-700">الصورة المميزة</label>
                @if ($existingFeaturedImage && ! $featured_image)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($existingFeaturedImage) }}" alt="" class="mb-2 h-16 w-24 rounded-lg object-cover">
                @endif
                <input id="featured_image" type="file" wire:model="featured_image" accept="image/*"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-bold file:text-primary-700">
                @error('featured_image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" wire:model="is_published" class="h-4 w-4 text-primary-600"> نشر المقال
        </label>

        <button type="submit" wire:loading.attr="disabled" wire:target="save,featured_image"
            class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white hover:bg-primary-700 disabled:opacity-60">
            <span wire:loading.remove wire:target="save">{{ $articleId ? 'حفظ التعديلات' : 'إنشاء المقال' }}</span>
            <span wire:loading wire:target="save">جارٍ الحفظ...</span>
        </button>
    </form>
</div>
