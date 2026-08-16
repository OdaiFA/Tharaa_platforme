@extends('layouts.admin')

@section('title', 'ملف مستخدم')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-primary-600 hover:underline">← كل المستخدمين</a>
        <h1 class="mt-1 text-2xl font-extrabold text-gray-900">{{ $user->name }}</h1>
        <p class="text-sm text-gray-500">{{ $user->email }}</p>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-gray-500">إجمالي الأرصدة</p>
            <p class="mt-1 text-xl font-extrabold text-gray-900">{{ number_format($totals['balance'], 2) }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-gray-500">المعاملات</p>
            <p class="mt-1 text-xl font-extrabold text-gray-900">{{ $totals['transactions'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-gray-500">الدورات المسجلة</p>
            <p class="mt-1 text-xl font-extrabold text-gray-900">{{ $totals['enrollments'] }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-6 max-w-lg space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        @csrf

        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">الاسم</label>
            <input id="name" type="text" name="name" value="{{ $user->name }}"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
        </div>

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
            <input id="email" type="email" name="email" value="{{ $user->email }}"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
        </div>

        <div>
            <label for="role" class="mb-1.5 block text-sm font-medium text-gray-700">الدور</label>
            <select id="role" name="role" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                <option value="user" @selected($user->role === 'user')>مستخدم</option>
                <option value="admin" @selected($user->role === 'admin')>مدير</option>
            </select>
        </div>

        <div>
            <label for="financial_level" class="mb-1.5 block text-sm font-medium text-gray-700">المستوى المالي</label>
            <select id="financial_level" name="financial_level" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                <option value="beginner" @selected($user->financial_level === 'beginner')>مبتدئ</option>
                <option value="intermediate" @selected($user->financial_level === 'intermediate')>متوسط</option>
                <option value="advanced" @selected($user->financial_level === 'advanced')>متقدم</option>
            </select>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="is_active" value="1" @checked($user->is_active) class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            حساب نشط
        </label>

        <button type="submit" class="w-full rounded-xl bg-primary-600 py-2.5 font-bold text-white hover:bg-primary-700">حفظ</button>
    </form>
@endsection
