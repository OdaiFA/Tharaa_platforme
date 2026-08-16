@extends('layouts.app')

@section('title', 'هدف جديد')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-2xl font-extrabold text-gray-900">إنشاء هدف جديد</h1>
        <p class="mt-1 text-sm text-gray-500">حدد ما تريد الادخار له</p>

        <form method="POST" action="{{ route('goals.store') }}" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf

            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">اسم الهدف</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="مثال: سيارة جديدة"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="target_amount" class="mb-1.5 block text-sm font-medium text-gray-700">المبلغ المستهدف</label>
                <input id="target_amount" type="number" step="0.01" min="1" name="target_amount" value="{{ old('target_amount') }}" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="current_amount" class="mb-1.5 block text-sm font-medium text-gray-700">المبلغ الحالي (اختياري)</label>
                <input id="current_amount" type="number" step="0.01" min="0" name="current_amount" value="{{ old('current_amount', 0) }}"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="deadline" class="mb-1.5 block text-sm font-medium text-gray-700">الموعد النهائي (اختياري)</label>
                <input id="deadline" type="date" name="deadline" value="{{ old('deadline') }}" min="{{ now()->format('Y-m-d') }}"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="priority" class="mb-1.5 block text-sm font-medium text-gray-700">الأولوية</label>
                <select id="priority" name="priority"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="low" @selected(old('priority') === 'low')>منخفضة</option>
                    <option value="medium" @selected(old('priority', 'medium') === 'medium')>متوسطة</option>
                    <option value="high" @selected(old('priority') === 'high')>عالية</option>
                </select>
            </div>

            <div>
                <label for="icon" class="mb-1.5 block text-sm font-medium text-gray-700">رمز تعبيري (اختياري)</label>
                <input id="icon" type="text" name="icon" value="{{ old('icon') }}" maxlength="16" placeholder="🎯"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <button type="submit" class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                إنشاء الهدف
            </button>
        </form>
    </div>
@endsection
