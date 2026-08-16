@extends('layouts.app')

@section('title', 'تعديل هدف')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-2xl font-extrabold text-gray-900">تعديل الهدف</h1>

        <form method="POST" action="{{ route('goals.update', $goal) }}" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">اسم الهدف</label>
                <input id="name" type="text" name="name" value="{{ old('name', $goal->name) }}" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="target_amount" class="mb-1.5 block text-sm font-medium text-gray-700">المبلغ المستهدف</label>
                <input id="target_amount" type="number" step="0.01" min="1" name="target_amount" value="{{ old('target_amount', $goal->target_amount) }}" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="deadline" class="mb-1.5 block text-sm font-medium text-gray-700">الموعد النهائي</label>
                <input id="deadline" type="date" name="deadline" value="{{ old('deadline', optional($goal->deadline)->format('Y-m-d')) }}"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="priority" class="mb-1.5 block text-sm font-medium text-gray-700">الأولوية</label>
                <select id="priority" name="priority"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="low" @selected(old('priority', $goal->priority) === 'low')>منخفضة</option>
                    <option value="medium" @selected(old('priority', $goal->priority) === 'medium')>متوسطة</option>
                    <option value="high" @selected(old('priority', $goal->priority) === 'high')>عالية</option>
                </select>
            </div>

            <div>
                <label for="icon" class="mb-1.5 block text-sm font-medium text-gray-700">رمز تعبيري</label>
                <input id="icon" type="text" name="icon" value="{{ old('icon', $goal->icon) }}" maxlength="16"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <button type="submit" class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                حفظ التعديلات
            </button>
        </form>
    </div>
@endsection
