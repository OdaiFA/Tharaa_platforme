@extends('layouts.app')

@section('title', $course->title)

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">{{ $course->title }}</h1>
            <p class="mt-1 text-sm text-gray-500">تقدمك في الدورة: {{ $enrollment->progress_percentage }}%</p>
        </div>
        <a href="{{ route('courses.show', $course) }}" class="text-sm font-medium text-primary-600 hover:underline">عرض تفاصيل الدورة</a>
    </div>

    <div class="mb-6 h-2.5 overflow-hidden rounded-full bg-gray-100">
        <div class="h-full rounded-full bg-primary-500 transition-all" style="width: {{ $enrollment->progress_percentage }}%"></div>
    </div>

    <div class="space-y-6">
        @foreach ($course->modules as $module)
            <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                <div class="border-b border-gray-50 bg-gray-50/60 p-4">
                    <h2 class="font-bold text-gray-900">{{ $module->title }}</h2>
                    @if ($module->description)
                        <p class="mt-0.5 text-sm text-gray-500">{{ $module->description }}</p>
                    @endif
                </div>

                <div class="divide-y divide-gray-50">
                    @foreach ($module->lessons as $lesson)
                        @php
                            $completed = in_array($lesson->id, $completedLessons);
                        @endphp
                        <div class="flex items-center justify-between p-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full {{ $completed ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-400' }}">
                                    @if ($completed)
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        <span class="text-xs font-bold">{{ $loop->iteration }}</span>
                                    @endif
                                </span>
                                <div>
                                    <p class="text-sm font-medium {{ $completed ? 'text-gray-400 line-through' : 'text-gray-800' }}">{{ $lesson->title }}</p>
                                    @if ($lesson->video_url)
                                        <p class="text-xs text-gray-400">📹 درس فيديو</p>
                                    @else
                                        <p class="text-xs text-gray-400">📄 درس نصي</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                @if ($lesson->quiz)
                                    <a href="{{ route('quizzes.show', $lesson->quiz) }}" class="rounded-lg bg-purple-50 px-3 py-1.5 text-xs font-bold text-purple-700 hover:bg-purple-100">
                                        اختبار
                                    </a>
                                @endif
                                @if (! $completed)
                                    <form method="POST" action="{{ route('lessons.complete', $lesson) }}">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-bold text-primary-700 hover:bg-primary-100">
                                            إكمال الدرس
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    @if ($enrollment->progress_percentage >= 100)
        <div class="mt-6 rounded-2xl border border-gold-200 bg-gold-500/10 p-6 text-center">
            <h2 class="text-xl font-extrabold text-amber-700">🎉 مبارك! أكملت الدورة</h2>
            <p class="mt-1 text-sm text-amber-600">حصلت على شهادة إتمام دورة «{{ $course->title }}»</p>
        </div>
    @endif
@endsection
