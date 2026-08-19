@extends('layouts.admin')

@section('title', 'ملف مستخدم')

@section('content')
    <livewire:admin.users.user-show :user="$user" />

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
