@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-2xl font-extrabold text-gray-900">الملف الشخصي</h1>
        <p class="mt-1 text-sm text-gray-500">حدّث بياناتك الشخصية</p>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf

            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">الاسم الكامل</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="date_of_birth" class="mb-1.5 block text-sm font-medium text-gray-700">تاريخ الميلاد</label>
                <input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}" max="{{ now()->subYears(7)->format('Y-m-d') }}"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                @if ($user->ageGroup)
                    <p class="mt-1 text-xs text-gray-400">فئتك العمرية: {{ $user->ageGroup->name }}</p>
                @endif
            </div>

            <div>
                <label for="financial_level" class="mb-1.5 block text-sm font-medium text-gray-700">المستوى المالي</label>
                <select id="financial_level" name="financial_level" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="beginner" @selected(old('financial_level', $user->financial_level) === 'beginner')>مبتدئ</option>
                    <option value="intermediate" @selected(old('financial_level', $user->financial_level) === 'intermediate')>متوسط</option>
                    <option value="advanced" @selected(old('financial_level', $user->financial_level) === 'advanced')>متقدم</option>
                </select>
            </div>

            <div>
                <label for="currency" class="mb-1.5 block text-sm font-medium text-gray-700">العملة</label>
                <input id="currency" type="text" name="currency" value="{{ old('currency', $user->currency) }}" maxlength="3"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            </div>

            <div>
                <label for="avatar" class="mb-1.5 block text-sm font-medium text-gray-700">الصورة الشخصية</label>
                <input id="avatar" type="file" name="avatar" accept="image/*"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-bold file:text-primary-700 hover:file:bg-primary-100">
            </div>

            <button type="submit" class="w-full rounded-xl bg-primary-600 py-3 font-bold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-700">
                حفظ التعديلات
            </button>
        </form>
    </div>
@endsection
