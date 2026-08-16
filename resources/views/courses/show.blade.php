@extends('layouts.app')

@section('title', $course->title)

@section('content')
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="flex h-44 items-center justify-center bg-gradient-to-br from-primary-600 to-primary-400 text-5xl">
            {{ $course->thumbnail ? '' : '📚' }}
        </div>
        <div class="p-6">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-primary-50 px-3 py-1 text-xs font-bold text-primary-700">
                    {{ match ($course->level) { 'beginner' => 'مستوى مبتدئ', 'intermediate' => 'مستوى متوسط', 'advanced' => 'مستوى متقدم', default => $course->level } }}
                </span>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-600">{{ $course->duration_hours }} ساعة</span>
                @if ($course->certificate_enabled)
                    <span class="rounded-full bg-gold-500/10 px-3 py-1 text-xs font-bold text-amber-700">🎓 شهادة إتمام</span>
                @endif
            </div>

            <h1 class="mt-3 text-2xl font-extrabold text-gray-900">{{ $course->title }}</h1>
            <p class="mt-2 leading-relaxed text-gray-600">{{ $course->description }}</p>

            @if ($course->ageGroups->isNotEmpty())
                <p class="mt-3 text-xs text-gray-400">
                    مناسب للفئات:
                    {{ $course->ageGroups->pluck('name')->implode('، ') }}
                </p>
            @endif

            <div class="mt-6">
                @guest
                    <a href="{{ route('login') }}" class="inline-block rounded-xl bg-primary-600 px-6 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                        سجل الدخول للانضمام
                    </a>
                @elseif ($enrollment)
                    <a href="{{ route('courses.learn', $course) }}" class="inline-block rounded-xl bg-primary-600 px-6 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                        متابعة التعلم ({{ $enrollment->progress_percentage }}%)
                    </a>
                @else
                    <form method="POST" action="{{ route('courses.enroll', $course) }}" class="inline">
                        @csrf
                        <button type="submit" class="rounded-xl bg-primary-600 px-6 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                            التسجيل في الدورة
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <h2 class="mt-8 mb-3 font-bold text-gray-900">محتوى الدورة</h2>
    <div class="space-y-3">
        @forelse ($course->modules->loadMissing('lessons') as $module)
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <h3 class="font-bold text-gray-900">{{ $module->title }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ $module->description }}</p>
                <ul class="mt-3 space-y-1">
                    @foreach ($module->lessons as $lesson)
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="h-4 w-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $lesson->title }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <p class="rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-400">سيُضاف محتوى الدورة قريباً</p>
        @endforelse
    </div>
@endsection
