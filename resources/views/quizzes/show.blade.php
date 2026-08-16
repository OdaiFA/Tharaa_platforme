@extends('layouts.app')

@section('title', 'اختبار: ' . $quiz->title)

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <h1 class="text-2xl font-extrabold text-gray-900">{{ $quiz->title }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $quiz->description }} ·
                المحاولة رقم {{ $attemptCount + 1 }} من {{ $quiz->max_attempts }}
            </p>
        </div>

        <form method="POST" action="{{ route('quizzes.submit', $quiz) }}" class="space-y-6">
            @csrf
            @method('POST')

            @foreach ($quiz->questions as $index => $question)
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <p class="font-bold text-gray-900">
                        <span class="text-primary-600">{{ $index + 1 }}.</span>
                        {{ $question->question }}
                    </p>

                    @if ($question->type === 'true_false')
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 p-3 text-sm hover:border-primary-400">
                                <input type="radio" name="answers[{{ $question->id }}]" value="true" class="h-4 w-4 text-primary-600 focus:ring-primary-500">
                                صحيح
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 p-3 text-sm hover:border-primary-400">
                                <input type="radio" name="answers[{{ $question->id }}]" value="false" class="h-4 w-4 text-primary-600 focus:ring-primary-500">
                                خطأ
                            </label>
                        </div>
                    @else
                        <div class="mt-4 space-y-2">
                            @foreach ($question->options as $option)
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 p-3 text-sm hover:border-primary-400">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" class="h-4 w-4 text-primary-600 focus:ring-primary-500">
                                    {{ $option }}
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            <button type="submit" class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                إرسال الاختبار
            </button>
        </form>
    </div>
@endsection
