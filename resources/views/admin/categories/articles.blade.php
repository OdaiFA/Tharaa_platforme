@extends('layouts.admin')

@section('title', 'تصنيفات المقالات')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">تصنيفات المقالات</h1>
        <p class="mt-1 text-sm text-gray-500">تنظيم المقالات التثقيفية حسب الموضوع</p>
    </div>

    <form method="POST" action="{{ route('admin.article-categories.store') }}" class="mb-6 grid grid-cols-1 gap-3 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm md:grid-cols-3">
        @csrf
        <input type="text" name="name" placeholder="اسم التصنيف" required
            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
        <input type="text" name="slug" placeholder="الرابط المختصر (slug)" required
            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
        <input type="text" name="description" placeholder="وصف (اختياري)"
            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
        <button type="submit" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-700 md:col-span-3">+ إضافة</button>
    </form>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($categories as $category)
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-800">{{ $category->name }}</p>
                        <p class="text-xs text-gray-400">{{ $category->articles_count }} مقال · /{{ $category->slug }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.article-categories.destroy', $category) }}" onsubmit="return confirm('حذف التصنيف؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">حذف</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-400">لا توجد تصنيفات</p>
        @endforelse
    </div>
@endsection
