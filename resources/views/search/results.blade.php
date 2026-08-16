@extends('layouts.app')

@section('title', 'نتائج البحث')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">نتائج البحث عن «{{ $term }}»</h1>
    </div>

    @if ($term === '')
        <p class="rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center text-gray-400">اكتب كلمة بحث في الأعلى</p>
    @else
        <h2 class="mb-3 font-bold text-gray-900">الدورات ({{ $courses->count() }})</h2>
        <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2">
            @forelse ($courses as $course)
                <a href="{{ route('courses.show', $course) }}" class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm hover:shadow-md">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-2xl">📚</span>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $course->title }}</h3>
                        <p class="text-sm text-gray-500">{{ $course->description }}</p>
                    </div>
                </a>
            @empty
                <p class="text-sm text-gray-400">لا توجد دورات مطابقة</p>
            @endforelse
        </div>

        <h2 class="mb-3 font-bold text-gray-900">المقالات ({{ $articles->count() }})</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @forelse ($articles as $article)
                <a href="{{ route('articles.show', $article) }}" class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm hover:shadow-md">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gray-50 text-2xl">📰</span>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $article->title }}</h3>
                        <p class="text-sm text-gray-500">{{ $article->excerpt }}</p>
                    </div>
                </a>
            @empty
                <p class="text-sm text-gray-400">لا توجد مقالات مطابقة</p>
            @endforelse
        </div>
    @endif
@endsection
