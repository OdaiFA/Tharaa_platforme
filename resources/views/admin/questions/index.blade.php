@extends('layouts.admin')

@section('title', 'أسئلة الاختبار: ' . $quiz->title)

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.quizzes.index', $quiz->lesson) }}" class="text-sm font-medium text-primary-600 hover:underline">← الاختبار</a>
        <h1 class="mt-1 text-2xl font-extrabold text-gray-900">أسئلة اختبار «{{ $quiz->title }}»</h1>
    </div>

    <div x-data="{ type: 'multiple_choice' }" class="mb-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('admin.questions.store') }}" class="space-y-3">
            @csrf
            <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">نص السؤال</label>
                <textarea name="question" rows="2" required placeholder="اكتب السؤال..."
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700">نوع السؤال</label>
                <select name="type" x-model="type" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-primary-500 focus:outline-none">
                    <option value="multiple_choice">اختيار من متعدد</option>
                    <option value="true_false">صحيح / خطأ</option>
                </select>
            </div>

            <template x-if="type === 'true_false'">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">الإجابة الصحيحة</label>
                    <select name="correct_answer[]" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-primary-500 focus:outline-none">
                        <option value="true">صحيح</option>
                        <option value="false">خطأ</option>
                    </select>
                </div>
            </template>

            <template x-if="type === 'multiple_choice'">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">الخيارات (من 2 إلى 6)</label>
                    <div class="space-y-2">
                        <template x-for="(option, i) in [...Array(4)]" :key="i">
                            <div class="flex items-center gap-2">
                                <input :name="`options[${i}]`" :placeholder="`الخيار ${i + 1}`" required
                                    class="flex-1 rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-primary-500 focus:outline-none">
                                <label class="flex items-center gap-1 text-xs text-gray-500">
                                    <input :name="`correct_answer[${i}]`" type="radio" :value="i"
                                        class="h-4 w-4 text-primary-600 focus:ring-primary-500"> صحيح
                                </label>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <div class="flex items-center gap-3 pt-2">
                <label class="text-sm font-medium text-gray-700">النقاط</label>
                <input type="number" name="points" value="1" min="1" class="w-24 rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-primary-500 focus:outline-none">
                <button type="submit" class="mr-auto rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-primary-700">+ إضافة السؤال</button>
            </div>
        </form>
    </div>

    <div class="space-y-3">
        @forelse ($quiz->questions as $question)
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-bold text-gray-900">{{ $question->question }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $question->type === 'true_false' ? 'صحيح/خطأ' : 'اختيار من متعدد' }} · {{ $question->points }} نقطة</p>
                        @if ($question->options)
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($question->options as $index => $option)
                                    <span class="rounded-lg bg-gray-50 px-2.5 py-1 text-xs {{ in_array((string) $index, array_map('strval', $question->correct_answer ?? [])) ? 'bg-green-50 font-bold text-green-700' : 'text-gray-600' }}">
                                        {{ $option }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('حذف السؤال؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">حذف</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-400">لا توجد أسئلة بعد</p>
        @endforelse
    </div>
@endsection
