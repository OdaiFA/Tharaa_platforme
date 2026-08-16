@extends('layouts.app')

@section('title', 'استنفدت المحاولات')

@section('content')
    <div class="mx-auto max-w-md py-12 text-center">
        <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
            <span class="text-5xl">😕</span>
            <h1 class="mt-4 text-xl font-extrabold text-gray-900">انتهت محاولاتك</h1>
            <p class="mt-2 text-sm text-gray-500">
                لقد استنفدت جميع المحاولات المسموحة لاختبار «{{ $quiz->title }}»
                ({{ $quiz->max_attempts }} محاولات).
            </p>
            <a href="{{ route('courses.learn', $quiz->lesson->module->course) }}" class="mt-6 inline-block rounded-xl bg-primary-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                العودة إلى الدورة
            </a>
        </div>
    </div>
@endsection
