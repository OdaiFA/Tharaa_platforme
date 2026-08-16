@extends('layouts.admin')

@section('title', 'اختبار الدرس: ' . $lesson->title)

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.lessons.index', $lesson->module) }}" class="text-sm font-medium text-primary-600 hover:underline">← دروس الوحدة</a>
        <h1 class="mt-1 text-2xl font-extrabold text-gray-900">اختبار درس «{{ $lesson->title }}»</h1>
    </div>

    <form method="POST" action="{{ route('admin.quizzes.store') }}" class="mb-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        @csrf
        <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <input type="text" name="title" placeholder="عنوان الاختبار" value="{{ old('title', $lesson->quiz?->title) }}" required
                class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none md:col-span-2">
            <input type="number" name="passing_score" placeholder="درجة النجاح %" value="{{ old('passing_score', $lesson->quiz?->passing_score ?? 60) }}" min="0" max="100"
                class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
            <input type="number" name="max_attempts" placeholder="عدد المحاولات" value="{{ old('max_attempts', $lesson->quiz?->max_attempts ?? 3) }}" min="1" max="10"
                class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
        </div>
        <div class="mt-3 flex items-center gap-4">
            <input type="text" name="description" placeholder="وصف الاختبار (اختياري)" value="{{ old('description', $lesson->quiz?->description) }}"
                class="flex-1 rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
            <button type="submit" class="rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-primary-700">حفظ</button>
        </div>
    </form>

    @if ($lesson->quiz)
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">أسئلة الاختبار ({{ $lesson->quiz->questions->count() }})</h2>
            <a href="{{ route('admin.questions.index', $lesson->quiz) }}" class="rounded-lg bg-purple-50 px-3 py-1.5 text-xs font-bold text-purple-700 hover:bg-purple-100">
                إدارة الأسئلة
            </a>
        </div>
        <div class="space-y-2">
            @forelse ($lesson->quiz->questions as $question)
                <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-white p-4 text-sm">
                    <div>
                        <p class="font-medium text-gray-800">{{ $question->question }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $question->type === 'true_false' ? 'صحيح/خطأ' : 'اختيار من متعدد' }} · {{ $question->points }} نقطة
                        </p>
                    </div>
                    <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('حذف السؤال؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">حذف</button>
                    </form>
                </div>
            @empty
                <p class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-400">لا توجد أسئلة بعد</p>
            @endforelse
        </div>
    @endif
@endsection
