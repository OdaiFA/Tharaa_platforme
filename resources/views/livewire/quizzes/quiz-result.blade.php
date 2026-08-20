<div class="mx-auto max-w-md py-8 text-center">
    <div class="rounded-2xl border {{ $attempt->is_passed ? 'border-green-200' : 'border-red-100' }} bg-white p-8 shadow-sm">
        @if ($attempt->is_passed)
            <span class="text-5xl">🎉</span>
            <h1 class="mt-4 text-xl font-extrabold text-green-700">مبروك! نجحت في الاختبار</h1>
        @else
            <span class="text-5xl">💪</span>
            <h1 class="mt-4 text-xl font-extrabold text-red-600">لم تنجح هذه المرة</h1>
        @endif

        <p class="mt-3 text-4xl font-extrabold text-gray-900">{{ $attempt->score }}%</p>
        <p class="mt-1 text-sm text-gray-500">
            حصلت على {{ $attempt->earned_points }} من {{ $attempt->total_points }} نقطة
            (نسبة النجاح: {{ $quiz->passing_score }}%)
        </p>

        @if ($canRetry)
            <a href="{{ route('quizzes.show', $quiz) }}" class="mt-6 inline-block rounded-xl bg-primary-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                إعادة المحاولة
            </a>
        @endif

        <a href="{{ route('courses.learn', $quiz->lesson->module->course) }}" class="mt-3 block text-sm font-bold text-primary-600 hover:underline">
            العودة إلى الدورة
        </a>
    </div>
</div>
