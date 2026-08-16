@extends('layouts.admin')

@section('title', 'وحدات: ' . $course->title)

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.courses.index') }}" class="text-sm font-medium text-primary-600 hover:underline">← كل الدورات</a>
        <h1 class="mt-1 text-2xl font-extrabold text-gray-900">وحدات دورة «{{ $course->title }}»</h1>
    </div>

    <form method="POST" action="{{ route('admin.modules.store') }}" class="mb-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        @csrf
        <input type="hidden" name="course_id" value="{{ $course->id }}">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <input type="text" name="title" placeholder="عنوان الوحدة" required
                class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
            <input type="text" name="description" placeholder="وصف الوحدة (اختياري)"
                class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
            <div class="flex gap-2">
                <input type="number" name="order_index" placeholder="الترتيب" min="0" class="w-24 rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                <button type="submit" class="flex-1 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-700">+ إضافة</button>
            </div>
        </div>
    </form>

    <div class="space-y-4">
        @forelse ($course->modules as $module)
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-gray-900">{{ $module->order_index ?: '' }} {{ $module->title }}</h2>
                        <p class="mt-0.5 text-sm text-gray-500">{{ $module->description }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $module->lessons->count() }} درس</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.lessons.index', $module) }}" class="rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-bold text-primary-700 hover:bg-primary-100">الدرس</a>
                        <form method="POST" action="{{ route('admin.modules.destroy', $module) }}" onsubmit="return confirm('حذف الوحدة؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100">حذف</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="text-gray-400">لا توجد وحدات بعد</p>
            </div>
        @endforelse
    </div>
@endsection
