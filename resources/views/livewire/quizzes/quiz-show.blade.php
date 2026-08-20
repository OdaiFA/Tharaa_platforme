<div class="mx-auto max-w-2xl">
    @if ($exhausted)
        <div class="py-12 text-center">
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
    @else
        <div class="mb-6">
            <h1 class="text-2xl font-extrabold text-gray-900">{{ $quiz->title }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $quiz->description }} ·
                المحاولة رقم {{ $attemptCount + 1 }} من {{ $quiz->max_attempts }}
            </p>
        </div>

        @error('quiz')
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">{{ $message }}</div>
        @enderror

        <form wire:submit="submit" class="space-y-6">
            @foreach ($quiz->questions as $index => $question)
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <p class="font-bold text-gray-900">
                        <span class="text-primary-600">{{ $index + 1 }}.</span>
                        {{ $question->question }}
                    </p>

                    @if ($question->type === 'true_false')
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 p-3 text-sm hover:border-primary-400">
                                <input type="radio" wire:model="answers.{{ $question->id }}" value="true" class="h-4 w-4 text-primary-600 focus:ring-primary-500">
                                صحيح
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 p-3 text-sm hover:border-primary-400">
                                <input type="radio" wire:model="answers.{{ $question->id }}" value="false" class="h-4 w-4 text-primary-600 focus:ring-primary-500">
                                خطأ
                            </label>
                        </div>
                    @else
                        <div class="mt-4 space-y-2">
                            @foreach ($question->options as $optionIndex => $option)
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 p-3 text-sm hover:border-primary-400">
                                    <input type="radio" wire:model="answers.{{ $question->id }}" value="{{ $optionIndex }}" class="h-4 w-4 text-primary-600 focus:ring-primary-500">
                                    {{ $option }}
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700 disabled:opacity-60">
                <span wire:loading.remove wire:target="submit">إرسال الاختبار</span>
                <span wire:loading wire:target="submit">جارٍ الإرسال...</span>
            </button>
        </form>
    @endif
</div>
