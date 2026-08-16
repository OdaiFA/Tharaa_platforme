@extends('layouts.admin')

@section('title', 'دروس: ' . $module->title)

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.courses.index') }}" class="text-sm font-medium text-primary-600 hover:underline">← كل الدورات</a>
        <h1 class="mt-1 text-2xl font-extrabold text-gray-900">دروس الوحدة «{{ $module->title }}»</h1>
    </div>

    <form method="POST" action="{{ route('admin.lessons.store') }}" class="mb-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        @csrf
        <input type="hidden" name="module_id" value="{{ $module->id }}">
        <div class="space-y-3">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <input type="text" name="title" placeholder="عنوان الدرس" required
                    class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                <input type="url" name="video_url" placeholder="رابط الفيديو (اختياري)"
                    class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                <div class="flex gap-2">
                    <input type="number" name="duration_minutes" placeholder="المدة (دقائق)" min="0" class="w-28 rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                    <button type="submit" class="flex-1 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-700">+ إضافة</button>
                </div>
            </div>
            <textarea name="content" rows="3" placeholder="محتوى الدرس (اختياري)"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none"></textarea>
        </div>
    </form>

    <div class="space-y-4">
        @forelse ($module->lessons as $lesson)
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-gray-900">{{ $lesson->title }}</h2>
                        <p class="mt-0.5 line-clamp-1 text-sm text-gray-500">{{ $lesson->content ?: ($lesson->video_url ?: 'لا يوجد محتوى') }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $lesson->duration_minutes }} دقيقة</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.quizzes.index', $lesson) }}" class="rounded-lg bg-purple-50 px-3 py-1.5 text-xs font-bold text-purple-700 hover:bg-purple-100">الاختبار</a>
                        <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}" onsubmit="return confirm('حذف الدرس؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100">حذف</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="text-gray-400">لا توجد دروس بعد</p>
            </div>
        @endforelse
    </div>
@endsection
