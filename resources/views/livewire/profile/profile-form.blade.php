<div class="mx-auto max-w-xl">
    <h1 class="text-2xl font-extrabold text-gray-900">الملف الشخصي</h1>
    <p class="mt-1 text-sm text-gray-500">حدّث بياناتك الشخصية</p>

    @if (session('success'))
        <div class="mt-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <form wire:submit="save" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">الاسم الكامل</label>
            <input id="name" type="text" wire:model="name"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="date_of_birth" class="mb-1.5 block text-sm font-medium text-gray-700">تاريخ الميلاد</label>
            <input id="date_of_birth" type="date" wire:model="date_of_birth" max="{{ now()->subYears(7)->format('Y-m-d') }}"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('date_of_birth') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            @if ($ageGroupName)
                <p class="mt-1 text-xs text-gray-400">فئتك العمرية: {{ $ageGroupName }}</p>
            @endif
        </div>

        <div>
            <label for="financial_level" class="mb-1.5 block text-sm font-medium text-gray-700">المستوى المالي</label>
            <select id="financial_level" wire:model="financial_level" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                <option value="beginner">مبتدئ</option>
                <option value="intermediate">متوسط</option>
                <option value="advanced">متقدم</option>
            </select>
        </div>

        <div>
            <label for="currency" class="mb-1.5 block text-sm font-medium text-gray-700">العملة</label>
            <input id="currency" type="text" wire:model="currency" maxlength="3"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('currency') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="avatar" class="mb-1.5 block text-sm font-medium text-gray-700">الصورة الشخصية</label>
            @if ($existingAvatar && ! $avatar)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($existingAvatar) }}" alt="" class="mb-2 h-16 w-16 rounded-full object-cover">
            @endif
            <input id="avatar" type="file" wire:model="avatar" accept="image/*"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-bold file:text-primary-700 hover:file:bg-primary-100">
            @error('avatar') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="save"
            class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700 disabled:opacity-60">
            <span wire:loading.remove wire:target="save">حفظ التعديلات</span>
            <span wire:loading wire:target="save">جارٍ الحفظ...</span>
        </button>
    </form>
</div>
