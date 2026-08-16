@extends('layouts.app')

@section('title', 'الدورات الموصى بها')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">الدورات الموصى بها لك</h1>
        <p class="mt-1 text-sm text-gray-500">
            بناءً على فئتك العمرية
            @if (auth()->user()?->ageGroup)
                ({{ auth()->user()->ageGroup->name }})
            @endif
        </p>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($courses as $course)
            <a href="{{ route('courses.show', $course) }}" class="group overflow-hidden rounded-2xl border border-gold-200 bg-white shadow-sm transition hover:shadow-md">
                <div class="flex h-36 items-center justify-center bg-gradient-to-br from-gold-500 to-amber-500 text-4xl">
                    {{ $course->thumbnail ? '' : '🏆' }}
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-2">
                        <span class="rounded-full bg-gold-500/10 px-2.5 py-0.5 text-xs font-medium text-amber-700">موصى به</span>
                        <span class="text-xs text-gray-400">{{ $course->duration_hours }} ساعة</span>
                    </div>
                    <h2 class="mt-2 font-bold text-gray-900 group-hover:text-primary-700">{{ $course->title }}</h2>
                    <p class="mt-1 line-clamp-2 text-sm text-gray-500">{{ $course->description }}</p>
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="text-gray-500">لا توجد دورات موصى بها لفئتك العمرية حالياً</p>
                <a href="{{ route('courses.index') }}" class="mt-3 inline-block text-sm font-bold text-primary-600 hover:underline">تصفح جميع الدورات</a>
            </div>
        @endforelse
    </div>
@endsection
