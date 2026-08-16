@extends('layouts.admin')

@section('title', 'تصنيفات المعاملات')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">تصنيفات المعاملات</h1>
        <div class="mt-2 flex gap-2">
            <a href="{{ route('admin.categories.index', ['type' => 'expense']) }}"
                class="rounded-full px-4 py-1.5 text-sm font-medium {{ ($type ?? 'expense') === 'expense' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100' }}">المصاريف</a>
            <a href="{{ route('admin.categories.index', ['type' => 'income']) }}"
                class="rounded-full px-4 py-1.5 text-sm font-medium {{ ($type ?? '') === 'income' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100' }}">المداخيل</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.categories.store') }}" class="mb-6 grid grid-cols-1 gap-3 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm md:grid-cols-4">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="text" name="name" placeholder="اسم التصنيف" required
            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
        <input type="text" name="icon" placeholder="أيقونة (اختياري)" maxlength="255"
            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
        <input type="text" name="color" placeholder="#RRGGBB" pattern="#[0-9A-Fa-f]{6}"
            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
        <button type="submit" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-700">+ إضافة</button>
    </form>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($categories as $category)
            <div class="flex items-center justify-between rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl text-lg" style="background-color: {{ $category->color ? $category->color . '22' : '#f3f4f6' }}">
                        {{ $category->icon ?: '🏷️' }}
                    </span>
                    <div>
                        <p class="font-medium text-gray-800">{{ $category->name }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $category->type === 'expense' ? 'مصروف' : 'دخل' }}
                            @if ($category->is_system)
                                · <span class="text-primary-600">نظامي</span>
                            @endif
                        </p>
                    </div>
                </div>
                @if (! $category->is_system)
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('حذف التصنيف؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">حذف</button>
                    </form>
                @endif
            </div>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-400">لا توجد تصنيفات</p>
        @endforelse
    </div>
@endsection
